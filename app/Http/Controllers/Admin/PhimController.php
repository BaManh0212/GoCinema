<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phim;
use App\Models\DanhMuc;
use App\Models\NgonNgu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;


class PhimController extends Controller
{
    public function index(Request $request)
{
    $query = Phim::with(['danhMucs', 'ngonNgu']);

    // 🔍 Tìm kiếm theo tiêu đề
    if ($request->filled('search')) {
        $query->where('tieu_de', 'like', '%' . $request->search . '%');
    }

    // 🗂️ Lọc theo danh mục
    if ($request->filled('danh_muc_id')) {
        $query->whereHas('danhMucs', function ($q) use ($request) {
            $q->where('danh_muc.id', $request->danh_muc_id);
        });
    }

    // 🗣️ Lọc theo ngôn ngữ
    if ($request->filled('ngon_ngu_id')) {
        $query->where('ngon_ngu_id', $request->ngon_ngu_id);
    }

    // 🎞️ Lọc theo trạng thái dựa trên ngày
    $today = now()->toDateString();

    if ($request->filled('trang_thai')) {
        switch ($request->trang_thai) {
            case 0: // Ngưng chiếu
                $query->whereNotNull('ngay_ket_thuc')
                      ->whereDate('ngay_ket_thuc', '<', $today);
                break;

            case 1: // Đang chiếu
                $query->whereDate('ngay_cong_chieu', '<=', $today)
                      ->where(function($q) use ($today) {
                          $q->whereNull('ngay_ket_thuc')
                            ->orWhereDate('ngay_ket_thuc', '>=', $today);
                      });
                break;

            case 2: // Sắp chiếu
                $query->whereDate('ngay_cong_chieu', '>', $today);
                break;
        }
    }

    // 📅 Sắp xếp theo ngày công chiếu mới nhất
    $query->orderByDesc('ngay_cong_chieu');

    // 📄 Phân trang
    $phims = $query->paginate(5)->appends($request->query());

    // ⚙️ Thêm trạng thái chiếu cho hiển thị
    foreach ($phims as $phim) {
        $ngayBatDau = $phim->ngay_cong_chieu ? Carbon::parse($phim->ngay_cong_chieu) : null;
        $ngayKetThuc = $phim->ngay_ket_thuc ? Carbon::parse($phim->ngay_ket_thuc) : null;

        if ($ngayBatDau && now()->lt($ngayBatDau)) {
            $phim->trang_thai_chieu = 'Sắp chiếu';
            $phim->trang_thai_mau = 'bg-info text-dark';
        } elseif ($ngayKetThuc && now()->gt($ngayKetThuc)) {
            $phim->trang_thai_chieu = 'Ngưng chiếu';
            $phim->trang_thai_mau = 'bg-secondary text-white';
        } else {
            $phim->trang_thai_chieu = 'Đang chiếu';
            $phim->trang_thai_mau = 'bg-success text-white';
        }
    }

    $danhMucs = DanhMuc::all();
    $ngonNgus = NgonNgu::all();

    return view('admin.phim.index', compact('phims', 'danhMucs', 'ngonNgus'));
}


   public function trashed(Request $request)
    {
        $query = Phim::onlyTrashed()->with(['danhMucs', 'ngonNgu']);

        // 🔍 Tìm kiếm theo tiêu đề
        if ($request->filled('search')) {
            $query->where('tieu_de', 'like', '%' . $request->search . '%');
        }

        // 🗂️ Lọc theo danh mục
        if ($request->filled('danh_muc_id')) {
            $query->whereHas('danhMucs', function ($q) use ($request) {
                $q->where('danh_muc.id', $request->danh_muc_id);
            });
        }

        // 🗣️ Lọc theo ngôn ngữ
        if ($request->filled('ngon_ngu_id')) {
            $query->where('ngon_ngu_id', $request->ngon_ngu_id);
        }

        // 🎞️ Lọc theo trạng thái (0: ngưng chiếu, 1: đang chiếu, 2: sắp chiếu)
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // 📅 Sắp xếp theo ngày công chiếu giảm dần
        $query->orderByDesc('ngay_cong_chieu');

        // 📄 Phân trang
        $phims = $query->paginate(10)->appends($request->query());

        // ⚙️ Xác định trạng thái chiếu (để hiển thị label màu)
        foreach ($phims as $phim) {
            $today = now();
            $ngayBatDau = $phim->ngay_cong_chieu ? Carbon::parse($phim->ngay_cong_chieu) : null;
            $ngayKetThuc = $phim->ngay_ket_thuc ? Carbon::parse($phim->ngay_ket_thuc) : null;

            if ($ngayBatDau && $today->lt($ngayBatDau)) {
                $phim->trang_thai_chieu = 'Sắp chiếu';
                $phim->trang_thai_mau = 'bg-info text-dark';
            } elseif ($ngayKetThuc && $today->gt($ngayKetThuc)) {
                $phim->trang_thai_chieu = 'Ngưng chiếu';
                $phim->trang_thai_mau = 'bg-secondary text-white';
            } else {
                $phim->trang_thai_chieu = 'Đang chiếu';
                $phim->trang_thai_mau = 'bg-success text-white';
            }
        }

        // Danh sách dữ liệu cho dropdown
        $danhMucs = DanhMuc::all();
        $ngonNgus = NgonNgu::all();

        return view('admin.phim.trashed', compact('phims', 'danhMucs', 'ngonNgus'));
    }


    public function create()
    {
        $danhMucs = DanhMuc::all();
        $ngonNgus = NgonNgu::all();
        return view('admin.phim.create', compact('danhMucs', 'ngonNgus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
    'tieu_de' => 'required|string|max:255|unique:phim,tieu_de',
    'mo_ta' => 'nullable|string',
    'dao_dien' => 'required|string|max:255',
    'dien_vien' => 'required|string',
    'thoi_luong' => 'required|numeric|min:1',
    'danh_muc_ids' => 'required|array',
    'danh_muc_ids.*' => 'exists:danh_muc,id',
    'ngon_ngu_id' => 'required|exists:ngon_ngu,id',
    'trailer' => 'nullable|string|max:255',
    'phu_de' => 'boolean',
    'banner' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
    'ngay_cong_chieu' => 'required|date',
    'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_cong_chieu',
    'trang_thai' => 'nullable|in:0,1,2',
    'dinh_dang' => 'nullable|string|max:10',
    'do_tuoi_gioi_han' => 'nullable|string|max:10',
    'anh_poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
], [
    'tieu_de.required' => 'Vui lòng nhập tiêu đề phim.',
    'tieu_de.unique' => 'Tiêu đề phim đã tồn tại.',
    'tieu_de.max' => 'Tiêu đề phim không được vượt quá 255 ký tự.',
    'dao_dien.required' => 'Vui lòng nhập tên đạo diễn.',
    'dao_dien.max' => 'Tên đạo diễn không được vượt quá 255 ký tự.',
    'dien_vien.required' => 'Vui lòng nhập tên diễn viên.',
    'thoi_luong.required' => 'Vui lòng nhập thời lượng phim.',
    'thoi_luong.numeric' => 'Thời lượng phải là số.',
    'thoi_luong.min' => 'Thời lượng phải lớn hơn 0.',
    'danh_muc_ids.required' => 'Vui lòng chọn ít nhất một danh mục.',
    'danh_muc_ids.*.exists' => 'Danh mục không hợp lệ.',
    'ngon_ngu_id.required' => 'Vui lòng chọn ngôn ngữ.',
    'ngon_ngu_id.exists' => 'Ngôn ngữ không hợp lệ.',
    'ngay_cong_chieu.required' => 'Vui lòng chọn ngày công chiếu.',
    'ngay_ket_thuc.date' => 'Ngày kết thúc không hợp lệ.',
    'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày công chiếu.', // ✅ thêm dòng này
    'anh_poster.image' => 'File tải lên phải là hình ảnh.',
    'anh_poster.mimes' => 'Ảnh phải có định dạng jpeg, png, jpg hoặc gif.',
    'anh_poster.max' => 'Ảnh không được vượt quá 2MB.',
]);


        $posterPath = null;
        if ($request->hasFile('anh_poster')) {
            $posterPath = $request->file('anh_poster')->store('posters', 'public');
        }


        $bannerPath = null;
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
        }

        // Tạo slug thân thiện SEO và đảm bảo duy nhất
        $slug = Str::slug($validated['tieu_de']);
        $originalSlug = $slug;
        $counter = 1;
        while (Phim::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        $phim = Phim::create([
            'tieu_de' => $validated['tieu_de'],
            'slug' => $slug,
            'mo_ta' => $validated['mo_ta'] ?? null,
            'thoi_luong' => $validated['thoi_luong'],
            'ngon_ngu_id' => $validated['ngon_ngu_id'],
            'anh_poster' => $posterPath,
            'banner' => $bannerPath,
            'trailer' => $validated['trailer'] ?? null,
            'phu_de' => $validated['phu_de'] ?? false,
            'ngay_cong_chieu' => $validated['ngay_cong_chieu'],
            'ngay_ket_thuc' => $validated['ngay_ket_thuc'] ?? null,
            'trang_thai' => $validated['trang_thai'] ?? 1,
            'dinh_dang' => $validated['dinh_dang'] ?? '2D',
            'do_tuoi_gioi_han' => $validated['do_tuoi_gioi_han'] ?? null,
            'dao_dien' => $validated['dao_dien'],
            'dien_vien' => $validated['dien_vien'],
        ]);

        $phim->danhMucs()->sync($validated['danh_muc_ids']);

        return redirect()->route('admin.phim.index')->with('success', '🎬 Thêm phim thành công!');
    }

    public function edit($id)
    {
        $phim = Phim::with('danhMucs')->findOrFail($id);
        $danhMucs = DanhMuc::all();
        $ngonNgus = NgonNgu::all();
        return view('admin.phim.edit', compact('phim', 'danhMucs', 'ngonNgus'));
    }

    public function update(Request $request, $id)
    {
        $phim = Phim::findOrFail($id);

        $validated = $request->validate([
    'tieu_de' => 'required|string|max:255|unique:phim,tieu_de,' . $phim->id,
    'mo_ta' => 'nullable|string',
    'dao_dien' => 'required|string|max:255',
    'dien_vien' => 'required|string',
    'trailer' => 'nullable|url',
    'phu_de' => 'required|boolean',
    'thoi_luong' => 'required|integer|min:1',
    'ngay_cong_chieu' => 'required|date',
    'ngay_ket_thuc' => 'nullable|date|after_or_equal:ngay_cong_chieu', // ✅ Kiểm tra hợp lệ ngày kết thúc
    'do_tuoi_gioi_han' => 'nullable|string|max:10',
    'danh_muc_ids' => 'required|array',
    'danh_muc_ids.*' => 'exists:danh_muc,id',
    'ngon_ngu_id' => 'required|exists:ngon_ngu,id',
    'anh_poster' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    'banner' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:4096',
    'trang_thai' => 'nullable|in:0,1,2',
    'dinh_dang' => 'nullable|string|max:10',
], [
    'tieu_de.required' => 'Vui lòng nhập tiêu đề phim.',
    'tieu_de.unique' => 'Tiêu đề phim đã tồn tại.',
    'dao_dien.required' => 'Vui lòng nhập tên đạo diễn.',
    'dien_vien.required' => 'Vui lòng nhập tên diễn viên.',
    'phu_de.required' => 'Vui lòng chọn phụ đề.',
    'thoi_luong.required' => 'Vui lòng nhập thời lượng phim.',
    'thoi_luong.min' => 'Thời lượng phải lớn hơn 0.',
    'ngay_cong_chieu.required' => 'Vui lòng chọn ngày công chiếu.',
    'ngay_ket_thuc.date' => 'Ngày kết thúc không hợp lệ.',
    'ngay_ket_thuc.after_or_equal' => 'Ngày kết thúc phải sau ngày công chiếu.', // ✅ Thêm dòng này
    'danh_muc_ids.required' => 'Vui lòng chọn ít nhất một danh mục.',
    'danh_muc_ids.*.exists' => 'Danh mục không hợp lệ.',
    'ngon_ngu_id.required' => 'Vui lòng chọn ngôn ngữ.',
    'ngon_ngu_id.exists' => 'Ngôn ngữ không hợp lệ.',
]);


        if ($request->hasFile('anh_poster')) {
            if ($phim->anh_poster) {
                Storage::disk('public')->delete($phim->anh_poster);
            }
            $validated['anh_poster'] = $request->file('anh_poster')->store('posters', 'public');
        }


        if ($request->hasFile('banner')) {
            if ($phim->banner) {
                Storage::disk('public')->delete($phim->banner);
            }
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        // Nếu tiêu đề thay đổi, sinh slug mới và đảm bảo duy nhất
        $newSlug = Str::slug($validated['tieu_de']);
        $original = $newSlug;
        $i = 1;
        while (Phim::where('slug', $newSlug)->where('id', '!=', $phim->id)->exists()) {
            $newSlug = $original . '-' . $i++;
        }
        $validated['slug'] = $newSlug;

    // Ensure default values if not provided
    $validated['trang_thai'] = $validated['trang_thai'] ?? $phim->trang_thai ?? 1;
    $validated['dinh_dang'] = $validated['dinh_dang'] ?? $phim->dinh_dang ?? '2D';

    $phim->update($validated);
        $phim->danhMucs()->sync($validated['danh_muc_ids']);

        return redirect()->route('admin.phim.index')->with('success', '🎬 Cập nhật phim thành công!');
    }

    public function show($id)
    {
        $phim = Phim::with(['danhMucs', 'ngonNgu'])->findOrFail($id);
        return view('admin.phim.show', compact('phim'));
    }

    /**
     * Kiểm tra xem phim đã có suất chiếu chưa
     */
    private function hasShowtimes($phimId)
    {
        // Dùng withTrashed() để lấy phim ngay cả khi đã xóa mềm
        $phim = Phim::withTrashed()->with('suatChieus')->findOrFail($phimId);
        
        $showtimeCount = $phim->suatChieus->count();
        
        return [
            'hasShowtimes' => $showtimeCount > 0,
            'showtimeCount' => $showtimeCount
        ];
    }

    /**
     * Kiểm tra xem phim đã bán vé chưa
     */
    private function hasSoldTickets($phimId)
    {
        // Dùng withTrashed() để lấy phim ngay cả khi đã xóa mềm
        $phim = Phim::withTrashed()->with(['suatChieus.chiTietVe'])->findOrFail($phimId);
        
        $totalTickets = 0;
        foreach ($phim->suatChieus as $suatChieu) {
            $ticketCount = $suatChieu->chiTietVe->count();
            $totalTickets += $ticketCount;
        }
        
        return [
            'hasSoldTickets' => $totalTickets > 0,
            'ticketCount' => $totalTickets
        ];
    }
    
    /**
     * Xóa mềm phim (chỉ xóa khi chưa có suất chiếu)
     */
    public function destroy($id)
    {
        // Kiểm tra xem có suất chiếu chưa
        $checkShowtimes = $this->hasShowtimes($id);
        
        if ($checkShowtimes['hasShowtimes']) {
            // Kiểm tra thêm số vé đã bán
            $checkTickets = $this->hasSoldTickets($id);
            
            $message = 'Không thể xóa phim vì đã có ' . $checkShowtimes['showtimeCount'] . ' suất chiếu';
            if ($checkTickets['hasSoldTickets']) {
                $message .= ' và ' . $checkTickets['ticketCount'] . ' vé đã bán ra';
            }
            $message .= '. Vui lòng xóa các suất chiếu trước khi xóa phim.';
            
            return redirect()->back()->with('error', $message);
        }
        
        // Nếu chưa có suất chiếu, thực hiện xóa mềm
        $phim = Phim::with(['danhMucs'])->findOrFail($id);
        
        // Không xóa ảnh poster và banner - giữ lại cho thùng rác
        // if ($phim->anh_poster) {
        //     Storage::disk('public')->delete($phim->anh_poster);
        // }
        
        // if ($phim->banner) {
        //     Storage::disk('public')->delete($phim->banner);
        // }
        
        // Giữ lại các mối quan hệ danh mục - không xóa trước khi xóa mềm
        // $phim->danhMucs()->detach();
        
        // Xóa mềm phim
        $phim->delete();
        
        return redirect()->route('admin.phim.index')
            ->with('success', 'Đã xóa phim thành công!');
    }

    public function restore($id)
    {
        $phim = Phim::withTrashed()->findOrFail($id);
        $phim->restore();
        return redirect()->route('admin.phim.index')->with('success', 'Khôi phục phim thành công!');
    }

    /**
     * Xóa cứng phim (chỉ xóa khi chưa có suất chiếu)
     */
    public function forceDelete($id)
    {
        // Kiểm tra xem có suất chiếu chưa
        $checkShowtimes = $this->hasShowtimes($id);
        
        if ($checkShowtimes['hasShowtimes']) {
            // Kiểm tra thêm số vé đã bán
            $checkTickets = $this->hasSoldTickets($id);
            
            $message = 'Không thể xóa vĩnh viễn phim vì đã có ' . $checkShowtimes['showtimeCount'] . ' suất chiếu';
            if ($checkTickets['hasSoldTickets']) {
                $message .= ' và ' . $checkTickets['ticketCount'] . ' vé đã bán ra';
            }
            $message .= '. Vui lòng liên hệ quản trị viên nếu cần hỗ trợ.';
            
            return redirect()->back()->with('error', $message);
        }
        
        $phim = Phim::withTrashed()->with(['danhMucs'])->findOrFail($id);
        
        // Xóa ảnh poster nếu có
        if ($phim->anh_poster && Storage::disk('public')->exists($phim->anh_poster)) {
            Storage::disk('public')->delete($phim->anh_poster);
        }
        
        // Xóa ảnh banner nếu có
        if ($phim->banner && Storage::disk('public')->exists($phim->banner)) {
            Storage::disk('public')->delete($phim->banner);
        }
        
        // Xóa các mối quan hệ danh mục trước khi xóa cứng
        $phim->danhMucs()->detach();
        
        $phim->forceDelete();
        return redirect()->route('admin.phim.trashed')->with('success', 'Đã xóa vĩnh viễn phim.');
    }
}
