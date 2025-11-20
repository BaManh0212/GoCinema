<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PhongChieu;
use App\Models\DinhDang;
use App\Models\Rap; // 👈 nếu bạn có model Rap
use App\Models\Ghe;
use App\Models\SoDoGhe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Schema;

class PhongChieuController extends Controller
{
    public function index(Request $request)
    {
        $query = PhongChieu::with(['dinhDang', 'ghes'])
            ->withCount('ghes');

        // 🔍 Tìm kiếm theo tên hoặc mã phòng
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('ten', 'like', "%$q%")
                    ->orWhere('id', 'like', "%$q%");
            });
        }

        // 🏢 Lọc theo rạp chiếu (nếu có cột rap_id)
        if ($request->filled('rap_id')) {
            $query->where('rap_id', $request->rap_id);
        }

        // 📏 Sắp xếp
        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('ten', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('ten', 'desc');
                break;
            case 'seats_asc':
                $query->orderBy('ghes_count', 'asc');
                break;
            case 'seats_desc':
                $query->orderBy('ghes_count', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('id', 'asc');
        }

        $phongchieus = $query->paginate(10)->withQueryString();

        // 🏢 Lấy danh sách rạp cho dropdown lọc
      $raps = collect(); // tạm thời rỗng


        return view('admin.phong_chieu.index', compact('phongchieus', 'raps'));
    }

    public function create()
    {
        $dinhdangs = DinhDang::orderBy('ten')->get();
        return view('admin.phong_chieu.create', compact('dinhdangs'));
    }

    public function store(Request $request)
    {
    $validated = $request->validate([
        'ten' => 'required|string|max:255',
        'dinh_dang_id' => 'required|exists:dinh_dang,id',
        'trang_thai' => 'required|in:hoat_dong,bao_tri,ngung_su_dung',
        'so_do' => 'nullable|string',
        'so_hang' => 'required|integer|min:1|max:50',
        'so_cot' => 'required|integer|min:1|max:50',
    ], [
        'ten.required' => 'Tên phòng chiếu không được để trống.',
        'ten.max' => 'Tên phòng không được vượt quá 255 ký tự.',
        'dinh_dang_id.required' => 'Định dạng phòng chiếu là bắt buộc.',
        'dinh_dang_id.exists' => 'Định dạng không hợp lệ.',
        'trang_thai.required' => 'Trạng thái là bắt buộc.',
        'so_hang.required' => 'Số hàng là bắt buộc.',
        'so_hang.integer' => 'Số hàng phải là số nguyên.',
        'so_hang.min' => 'Số hàng tối thiểu là 1.',
        'so_hang.max' => 'Số hàng tối đa là 50.',
        'so_cot.required' => 'Số cột là bắt buộc.',
        'so_cot.integer' => 'Số cột phải là số nguyên.',
        'so_cot.min' => 'Số cột tối thiểu là 1.',
        'so_cot.max' => 'Số cột tối đa là 50.',
    ]);

    $validated['rap_id'] = 1;
    $validated['so_do'] = $request->input('so_do'); // thêm so_do vào validated

    if (PhongChieu::where('ten', $validated['ten'])->exists()) {
        return back()->withInput()->withErrors([
            'ten' => 'Phòng chiếu này đã tồn tại.'
        ]);
    }

    try {
        $phongChieu = PhongChieu::create($validated);

        // Tự động tạo ghế mặc định là 'thuong'
        $this->createDefaultSeats($phongChieu);

        return redirect()->route('admin.phongchieu.index')
            ->with('success', 'Thêm phòng chiếu thành công');
    } catch (\Exception $e) {
        return back()->withInput()->withErrors([
            'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
        ]);
    }
    }

    public function edit($id)
    {
        $phongchieu = PhongChieu::findOrFail($id);
        $dinhdangs = DinhDang::orderBy('ten')->get();
        return view('admin.phong_chieu.edit', compact('phongchieu', 'dinhdangs'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'ten' => 'required|string|max:255',
        'dinh_dang_id' => 'required|exists:dinh_dang,id',
            'trang_thai' => 'required|in:hoat_dong,bao_tri,ngung_su_dung',
            'so_do' => 'nullable|string',
            'so_hang' => 'required|integer|min:1|max:50',
            'so_cot' => 'required|integer|min:1|max:50',
        ], [
            'ten.required' => 'Tên phòng chiếu không được để trống.',
            'trang_thai.required' => 'Trạng thái là bắt buộc.',
            'dinh_dang_id.required' => 'Định dạng phòng chiếu là bắt buộc.',
            'dinh_dang_id.exists' => 'Định dạng không hợp lệ.',
            'so_hang.required' => 'Số hàng là bắt buộc.',
            'so_hang.integer' => 'Số hàng phải là số nguyên.',
            'so_hang.min' => 'Số hàng tối thiểu là 1.',
            'so_hang.max' => 'Số hàng tối đa là 50.',
            'so_cot.required' => 'Số cột là bắt buộc.',
            'so_cot.integer' => 'Số cột phải là số nguyên.',
            'so_cot.min' => 'Số cột tối thiểu là 1.',
            'so_cot.max' => 'Số cột tối đa là 50.',
        ]);

        $validated['rap_id'] = 1;

        if (PhongChieu::where('ten', $validated['ten'])
            ->where('id', '<>', $id)
            ->exists()) {
            return back()->withInput()->withErrors([
                'ten' => 'Phòng chiếu này đã tồn tại.'
            ]);
        }

        try {
            $phongchieu = PhongChieu::findOrFail($id);
            $phongchieu->update($validated);
            return redirect()->route('admin.phongchieu.index')
                ->with('success', 'Cập nhật phòng chiếu thành công');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $phongchieu = PhongChieu::findOrFail($id);

            // Không cho xóa nếu đang có bất kỳ suất chiếu nào
            $suatCount = $phongchieu->suatChieu()->count();
            Log::info('PhongChieu destroy requested', array_filter([
                'phong_id' => $id,
                'suat_count' => $suatCount,
                'ghe_count_before' => Schema::hasTable('ghe') ? DB::table('ghe')->where('phong_id', $id)->count() : null,
                'sodo_count_before' => Schema::hasTable('so_do_ghe') ? DB::table('so_do_ghe')->where('phong_id', $id)->count() : null,
            ], fn($v) => $v !== null));
            if ($suatCount > 0) {
                return redirect()->route('admin.phongchieu.index')
                    ->with('error', 'Không thể xóa phòng chiếu: phòng còn ' . $suatCount . ' suất chiếu liên kết.');
            }
            DB::transaction(function () use ($phongchieu, $id) {
                // Xóa sơ đồ ghế nếu có (nếu bảng tồn tại)
                if (Schema::hasTable('so_do_ghe')) {
                    SoDoGhe::where('phong_id', $phongchieu->id)->delete();
                } else {
                    Log::warning('Table so_do_ghe not found when deleting room', ['phong_id' => $id]);
                }
                // Xóa tất cả ghế thuộc phòng (hard delete để không bị FK chặn)
                if (Schema::hasTable('ghe')) {
                    DB::table('ghe')->where('phong_id', $phongchieu->id)->delete();
                } else {
                    Log::warning('Table ghe not found when deleting room', ['phong_id' => $id]);
                }
                // Xóa phòng
                $phongchieu->delete();
                Log::info('PhongChieu deleted in transaction', [
                    'phong_id' => $id,
                ]);
            });

            // Đảm bảo không còn ghế sót lại (phòng hờ đề phòng soft delete cấu hình khác)
            if (Schema::hasTable('ghe')) {
                $remainingGhe = DB::table('ghe')->where('phong_id', $id)->count();
                if ($remainingGhe > 0) {
                    DB::table('ghe')->where('phong_id', $id)->delete();
                    Log::warning('Force-deleted remaining ghe after transaction', [
                        'phong_id' => $id,
                        'remaining_ghe_deleted' => $remainingGhe,
                    ]);
                }
            }
            Log::info('PhongChieu destroy completed', array_filter([
                'phong_id' => $id,
                'ghe_count_after' => Schema::hasTable('ghe') ? DB::table('ghe')->where('phong_id', $id)->count() : null,
                'sodo_count_after' => Schema::hasTable('so_do_ghe') ? DB::table('so_do_ghe')->where('phong_id', $id)->count() : null,
            ], fn($v) => $v !== null));

            return redirect()->route('admin.phongchieu.index')
                ->with('success', 'Xóa phòng chiếu thành công');
        } catch (QueryException $e) {
            Log::error('PhongChieu destroy failed (QueryException)', [
                'phong_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('admin.phongchieu.index')
                ->with('error', 'Không thể xóa phòng: ràng buộc dữ liệu đang ngăn xóa. Chi tiết: ' . $e->getCode());
        } catch (\Exception $e) {
            Log::error('PhongChieu destroy failed', [
                'phong_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->route('admin.phongchieu.index')
                ->with('error', 'Có lỗi xảy ra khi xóa phòng: ' . $e->getMessage());
        }
    }

    /**
     * Tự động tạo ghế mặc định cho phòng chiếu mới
     */
    private function createDefaultSeats(PhongChieu $phongChieu)
    {
        $seats = [];

        for ($hangIndex = 0; $hangIndex < $phongChieu->so_hang; $hangIndex++) {
            $hang = chr(65 + $hangIndex); // A, B, C, ...

            for ($cot = 1; $cot <= $phongChieu->so_cot; $cot++) {
                $seats[] = [
                    'phong_id' => $phongChieu->id,
                    'hang' => $hang,
                    'cot' => $cot,
                    'loai' => 'thuong', // Mặc định là ghế thường
                    'trang_thai' => 'hoat_dong',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Ghe::insert($seats);
    }
}
