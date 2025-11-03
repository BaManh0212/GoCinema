<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    // Danh sách banner
    public function index()
    {
        $banners = Banner::orderBy('display_order', 'asc')->paginate(10);
        return view('admin.banner.index', compact('banners'));
    }

    // Form tạo mới
    public function create()
    {
        return view('admin.banner.form', ['banner' => new Banner()]);
    }

    // Lưu banner mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'nullable|string|max:255',
            'type'          => 'required|in:image,video',
            'image'         => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'video_file'    => 'nullable|file|mimes:mp4|max:102400', // 100MB
            'video_url'     => 'nullable|string|max:500',
            'link'          => 'nullable|url|max:255',
            'display_order' => 'nullable|integer|min:0',
            'start_at'      => 'nullable|date',
            'end_at'        => 'nullable|date|after_or_equal:start_at',
        ]);

        // Upload file nếu có
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('uploads/banners', 'public');
        }

        if ($request->hasFile('video_file')) {
            $validated['video_url'] = $request->file('video_file')->store('uploads/banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        Banner::create($validated);

        return redirect()->route('admin.banners.index')->with('success', '🟢 Banner đã được thêm thành công!');
    }

    // Form chỉnh sửa
    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banner.form', compact('banner'));
    }

    // Cập nhật banner
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validated = $request->validate([
            'title'         => 'nullable|string|max:255',
            'type'          => 'required|in:image,video',
            'image'         => 'nullable|file|mimes:jpg,jpeg,png,webp|max:5120',
            'video_file'    => 'nullable|file|mimes:mp4|max:102400',
            'video_url'     => 'nullable|string|max:500',
            'link'          => 'nullable|url|max:255',
            'display_order' => 'nullable|integer|min:0',
            'start_at'      => 'nullable|date',
            'end_at'        => 'nullable|date|after_or_equal:start_at',
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $validated['image'] = $request->file('image')->store('uploads/banners', 'public');
        }

        if ($request->hasFile('video_file')) {
            if ($banner->video_url && Storage::disk('public')->exists($banner->video_url)) {
                Storage::disk('public')->delete($banner->video_url);
            }
            $validated['video_url'] = $request->file('video_file')->store('uploads/banners', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $banner->update($validated);

        return redirect()->route('admin.banners.index')->with('success', '🟡 Banner đã được cập nhật thành công!');
    }

    // Xóa banner
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        if ($banner->video_url && Storage::disk('public')->exists($banner->video_url)) {
            Storage::disk('public')->delete($banner->video_url);
        }

        $banner->delete();

        return back()->with('success', '🔴 Banner đã bị xóa!');
    }

    // Bật/tắt banner
    public function toggle($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->is_active = !$banner->is_active;
        $banner->save();

        return back()->with('success', '🔁 Trạng thái banner đã được cập nhật!');
    }
}
