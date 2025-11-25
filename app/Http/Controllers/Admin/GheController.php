<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ghe;
use App\Models\PhongChieu;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\ChiTietVe;  // Assuming this model exists for booking details

class GheController extends Controller
{
    public function index($id)
    {
        $phong = PhongChieu::findOrFail($id);

        // Lấy tất cả ghế, nhóm theo hàng và cột để tạo ma trận
        $ghes = Ghe::where('phong_id', $id)
            ->orderBy('hang')
            ->orderBy('cot')
            ->get()
            ->keyBy(function ($item) {
                return $item->hang . '-' . $item->cot;
            });

        return view('admin.ghe.index', compact('phong', 'ghes'));
    }

    /**
     * Thêm hàng mới vào phòng chiếu
     */
    public function addRow(Request $request, $phong_id)
    {
        $request->validate([
            'hang' => 'required|string|max:5',
        ]);

        try {
            $phong = PhongChieu::findOrFail($phong_id);
            $hang = strtoupper($request->hang);

            // Kiểm tra hàng đã tồn tại chưa
            if (Ghe::where('phong_id', $phong_id)->where('hang', $hang)->exists()) {
                return response()->json(['success' => false, 'message' => 'Hàng này đã tồn tại']);
            }

            // Tạo ghế cho hàng mới
            for ($cot = 1; $cot <= $phong->so_cot; $cot++) {
                Ghe::create([
                    'phong_id' => $phong_id,
                    'hang' => $hang,
                    'cot' => $cot,
                    'loai' => 'thuong',
                    'trang_thai' => 'hoat_dong',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Đã thêm hàng mới']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Thêm cột mới vào phòng chiếu
     */
    public function addColumn(Request $request, $phong_id)
    {
        try {
            $phong = PhongChieu::findOrFail($phong_id);
            $newSoCot = $phong->so_cot + 1;

            // Cập nhật số cột trong phòng
            $phong->update(['so_cot' => $newSoCot]);

            // Lấy tất cả hàng hiện tại
            $hangs = Ghe::where('phong_id', $phong_id)
                ->distinct()
                ->pluck('hang')
                ->sort()
                ->values();

            // Thêm cột mới cho mỗi hàng
            foreach ($hangs as $hang) {
                Ghe::create([
                    'phong_id' => $phong_id,
                    'hang' => $hang,
                    'cot' => $newSoCot,
                    'loai' => 'thuong',
                    'trang_thai' => 'hoat_dong',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Đã thêm cột mới']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request, $phong_id)
    {
        $request->validate([
            'hang' => 'required|string|max:5',
            'cot' => 'required|integer|min:1',
            'loai' => 'required|in:thuong,vip,doi',
        ]);

        Ghe::create([
            'phong_id' => $phong_id,
            'hang' => strtoupper($request->hang),
            'cot' => $request->cot,
            'loai' => $request->loai,
            'trang_thai' => 'hoat_dong',
            'created_at' => now(),
        ]);

        return back()->with('success', 'Đã thêm ghế mới thành công!');
    }

    public function destroy($id)
    {
        $ghe = Ghe::findOrFail($id);
        $ghe->delete();

        return back()->with('success', 'Đã xóa ghế thành công!');
    }
    public function updateMap(Request $request, $phong_id)
    {
        $seats = $request->input('seats', []);

        // Validate seats data
        foreach ($seats as $index => $seat) {
            if (!isset($seat['hang']) || !is_string($seat['hang']) || strlen($seat['hang']) === 0) {
                return response()->json(['success' => false, 'message' => "Invalid seat 'hang' at index $index"], 400);
            }
            if (!isset($seat['cot']) || !is_numeric($seat['cot']) || $seat['cot'] < 1) {
                return response()->json(['success' => false, 'message' => "Invalid seat 'cot' at index $index"], 400);
            }
            if (!isset($seat['loai']) || !in_array($seat['loai'], ['thuong', 'vip', 'doi'], true)) {
                return response()->json(['success' => false, 'message' => "Invalid seat 'loai' at index $index"], 400);
            }
            if (!isset($seat['trang_thai']) || !in_array($seat['trang_thai'], ['hoat_dong', 'bao_tri'], true)) {
                return response()->json(['success' => false, 'message' => "Invalid seat 'trang_thai' at index $index"], 400);
            }
        }

        try {
            // Xóa tất cả chi tiết vé (booking details) liên quan đến các ghế phòng này
            $seatIds = Ghe::where('phong_id', $phong_id)->pluck('id');
            if ($seatIds->count() > 0) {
                DB::table('chi_tiet_ve')->whereIn('ghe_id', $seatIds)->delete();
            }

            // Xóa tất cả ghế cũ của phòng
            Ghe::where('phong_id', $phong_id)->delete();

            // Tạo ghế mới từ ma trận
            foreach ($seats as $seat) {
                Ghe::create([
                    'phong_id' => $phong_id,
                    'hang' => $seat['hang'],
                    'cot' => $seat['cot'],
                    'loai' => $seat['loai'],
                    'trang_thai' => $seat['trang_thai'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => true, 'redirect' => route('admin.phongchieu.index')]);
    }

    /**
     * Chuyển đổi hàng thành ghế VIP
     */
    public function convertRowsToVip(Request $request, $phong_id)
    {
        $request->validate([
            'rows' => 'required|array',
            'rows.*' => 'string|max:5',
        ]);

        try {
            $phong = PhongChieu::findOrFail($phong_id);

            foreach ($request->rows as $hang) {
                for ($cot = 1; $cot <= $phong->so_cot; $cot++) {
                    Ghe::updateOrCreate(
                        ['phong_id' => $phong_id, 'hang' => $hang, 'cot' => $cot],
                        ['loai' => 'vip', 'trang_thai' => 'hoat_dong', 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }

            return response()->json(['success' => true, 'message' => 'Đã chuyển đổi hàng thành ghế VIP']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Chuyển đổi hàng thành ghế thường
     */
    public function convertRowsToNormal(Request $request, $phong_id)
    {
        $request->validate([
            'rows' => 'required|array',
            'rows.*' => 'string|max:5',
        ]);

        try {
            $phong = PhongChieu::findOrFail($phong_id);

            foreach ($request->rows as $hang) {
                for ($cot = 1; $cot <= $phong->so_cot; $cot++) {
                    Ghe::updateOrCreate(
                        ['phong_id' => $phong_id, 'hang' => $hang, 'cot' => $cot],
                        ['loai' => 'thuong', 'trang_thai' => 'hoat_dong', 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }

            return response()->json(['success' => true, 'message' => 'Đã chuyển đổi hàng thành ghế thường']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Chuyển đổi ghế thành ghế đôi
     */
    public function convertToDoubleSeats(Request $request, $phong_id)
    {
        $request->validate([
            'rows' => 'required|array',
            'rows.*' => 'string|max:5',
        ]);

        try {
            $phong = PhongChieu::findOrFail($phong_id);

            foreach ($request->rows as $hang) {
                // Lấy tất cả ghế trong hàng này
                $seatsInRow = Ghe::where('phong_id', $phong_id)
                    ->where('hang', $hang)
                    ->orderBy('cot')
                    ->get();

                // Nếu hàng chưa có ghế, tạo tất cả ghế thường trước
                if ($seatsInRow->isEmpty()) {
                    for ($cot = 1; $cot <= $phong->so_cot; $cot++) {
                        Ghe::create([
                            'phong_id' => $phong_id,
                            'hang' => $hang,
                            'cot' => $cot,
                            'loai' => 'thuong',
                            'trang_thai' => 'hoat_dong',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                    // Lấy lại sau khi tạo
                    $seatsInRow = Ghe::where('phong_id', $phong_id)
                        ->where('hang', $hang)
                        ->orderBy('cot')
                        ->get();
                }

                // Thu thập ghế cần cập nhật và xóa
                $toUpdate = [];
                $toDelete = [];

                for ($i = 0; $i < $seatsInRow->count(); $i += 2) {
                    if ($i + 1 < $seatsInRow->count()) {
                        $toUpdate[] = $seatsInRow[$i]->id;
                        $toDelete[] = $seatsInRow[$i + 1]->id;
                    } else {
                        // Ghế lẻ cuối cùng cũng xóa luôn
                        $toDelete[] = $seatsInRow[$i]->id;
                    }
                }

                // Nếu số ghế là lẻ, ghế cuối cùng sẽ bị xóa
                // Nếu số ghế là chẵn, tất cả ghế sẽ được ghép đôi

                // Cập nhật ghế thành đôi
                if (!empty($toUpdate)) {
                    Ghe::whereIn('id', $toUpdate)->update(['loai' => 'doi', 'updated_at' => now()]);
                }

                // Xóa tất cả ghế thừa (bao gồm ghế lẻ cuối cùng)
                if (!empty($toDelete)) {
                    Ghe::whereIn('id', $toDelete)->delete();
                }
            }

            return response()->json(['success' => true, 'message' => 'Đã chuyển đổi thành ghế đôi']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

}
