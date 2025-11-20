<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SoDoGhe;
use App\Models\PhongChieu;
use App\Models\SuatChieu;

class SoDoGheController extends Controller
{
    public function index()
    {
        // Lấy tất cả phòng chiếu kèm sơ đồ ghế
    $phongs = PhongChieu::with('soDoGhe')->get();

    return view('admin.sodoghe.index', compact('phongs'));
    }

    public function create()
    {
        $phongs = PhongChieu::where('rap_id', 1)->get(); // chỉ có 1 rạp
        return view('admin.sodoghe.create', compact('phongs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'phong_id' => 'required|exists:phong_chieu,id',
            'ma_tran' => 'required|json',
        ]);

        SoDoGhe::create([
            'phong_id' => $request->phong_id,
            'ma_tran' => $request->ma_tran,
        ]);

        return redirect()->route('admin.sodo.index')->with('success', 'Đã lưu sơ đồ ghế thành công');
    }

public function show($phong_id)
{
    // Lấy thông tin phòng
    $phong = PhongChieu::findOrFail($phong_id);

    // Lấy sơ đồ ghế của phòng
    $sodoghe = SoDoGhe::where('phong_id', $phong_id)->first();

    if (!$sodoghe) {
        return back()->with('error', 'Phòng này chưa có sơ đồ ghế!');
    }

    // Giải mã ma trận JSON
    $matrix = json_decode($sodoghe->ma_tran, true);

    // Truyền luôn $sodoghe để tránh lỗi
    return view('admin.sodoghe.show', [
        'phong' => $phong,
        'matrix' => $matrix,
        'sodoghe' => $sodoghe
    ]);
}
public function show1(Request $request)
{
    $suatChieu = SuatChieu::with('phong.soDoGhe')->findOrFail($request->suat_chieu_id);
    $phong = $suatChieu->phong;

    if (!$phong || !$phong->soDoGhe) {
        return back()->with('error', 'Phòng này chưa có sơ đồ ghế!');
    }

    // Lấy ma trận ghế từ JSON
    $matrix = json_decode($phong->soDoGhe->ma_tran, true) ?: [];

    // Thêm id nếu chưa có
    $nextId = 1;
    foreach ($matrix as &$seat) {
        if (!isset($seat['id'])) $seat['id'] = $nextId++;
        $seat['gia'] = match($seat['loai'] ?? 'thuong') {
            'vip' => $this->calculateSeatPrice($suatChieu, (object)['loai' => 'vip']),
            'doi' => $this->calculateSeatPrice($suatChieu, (object)['loai' => 'doi']),
            default => $this->calculateSeatPrice($suatChieu, (object)['loai' => 'thuong']),
        };
    }

    // Ghế đã đặt
    $trangThaiGhe = DB::table('ghe_suat_chieu')
        ->where('suat_chieu_id', $suatChieu->id)
        ->pluck('trang_thai', 'so_do_id'); // chú ý key phải đúng với matrix['id'] hoặc so_do_id

    // Combo
    // $combos = Combo::all();

    return view('admin.suatchieu.show', compact(
        'suatChieu','phong','matrix','trangThaiGhe','combos'
    ));
}



    public function edit(SoDoGhe $sodo)
    {
        $phongs = PhongChieu::where('rap_id', 1)->get();
        return view('admin.sodoghe.edit', compact('sodo', 'phongs'));
    }

    // public function update(Request $request, SoDoGhe $sodo)
    // {
    //     $request->validate([
    //         'phong_id' => 'required|exists:phong_chieu,id',
    //         'ma_tran' => 'required|json',
    //     ]);

    //     $sodo->update([
    //         'phong_id' => $request->phong_id,
    //         'ma_tran' => $request->ma_tran,
    //     ]);

    //     return redirect()->route('admin.sodo.index')->with('success', 'Cập nhật sơ đồ ghế thành công');
    // }

    public function destroy(SoDoGhe $sodo)
    {
        $sodo->delete();
        return redirect()->route('admin.sodo.index')->with('success', 'Xóa sơ đồ ghế thành công');
    }
public function updateSeatStatus(Request $request)
{
    $phong_id = $request->phong_id;
    $matrixUpdate = $request->matrix;

    $sodoghe = SoDoGhe::where('phong_id', $phong_id)->firstOrFail();
    $matrix = json_decode($sodoghe->ma_tran, true);

    // Cập nhật trạng thái ghế
    foreach($matrix as &$seat){
        foreach($matrixUpdate as $u){
            if($seat['hang'] == $u['hang'] && $seat['cot'] == $u['cot']){
                $seat['trang_thai'] = $u['trang_thai'];
            }
        }
    }

    $sodoghe->ma_tran = json_encode($matrix);
    $sodoghe->save();

    return response()->json(['success'=>true]);
}

}
