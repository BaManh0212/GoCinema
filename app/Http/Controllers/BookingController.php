<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SuatChieu;
use App\Models\Ghe;
use App\Models\GheSuatChieu;
use App\Models\DonDatVe;
use App\Models\ChiTietVe;
use App\Models\MaGiamGia;
use App\Models\Combo;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Trang đặt vé
    public function index(Request $request)
    {
        $suatChieuId = $request->query('suat_chieu_id');
        if (!$suatChieuId) {
            return redirect('/')->with('error', 'Vui lòng chọn suất chiếu.');
        }

        $suatChieu = SuatChieu::with(['phim', 'phong.ghe'])->findOrFail($suatChieuId);

        if ($suatChieu->trang_thai !== 'hoat_dong' || Carbon::now()->gte($suatChieu->gio_bat_dau)) {
            return redirect('/')->with('error', 'Suất chiếu không khả dụng.');
        }

        $ghes = $suatChieu->phong->ghe->sortBy(['hang','cot'])->groupBy('hang');

        $gheStatuses = GheSuatChieu::where('suat_chieu_id', $suatChieuId)
            ->pluck('trang_thai','ghe_id')->toArray();

        $gheDaDat = ChiTietVe::where('suat_chieu_id',$suatChieuId)
            ->whereIn('trang_thai',['da_dat','da_thanh_toan','da_checkin'])
            ->pluck('ghe_id')->toArray();

        $giuTamIds = DB::table('ghe_giu_tam')
            ->where('suat_chieu_id',$suatChieuId)
            ->where('het_han','>',Carbon::now())
            ->pluck('ghe_id')->toArray();

        $combos = Combo::all();

        return view('client.booking.index', compact(
            'suatChieu','ghes','gheStatuses','gheDaDat','giuTamIds','combos'
        ));
    }

    // Giữ tạm ghế
    public function holdSeats(Request $request)
    {
        $request->validate([
            'suat_chieu_id'=>'required|exists:suat_chieu,id',
            'ghe_ids'=>'required|array|min:1|max:2',
            'ghe_ids.*'=>'exists:ghe,id',
        ]);

        $suatChieuId = $request->suat_chieu_id;
        $gheIds = $request->ghe_ids;

        DB::beginTransaction();
        try {
            foreach($gheIds as $gheId){
                $daDat = ChiTietVe::where('suat_chieu_id',$suatChieuId)
                    ->where('ghe_id',$gheId)
                    ->whereIn('trang_thai',['da_dat','da_thanh_toan','da_checkin'])
                    ->exists();
                if($daDat) throw new \Exception("Ghế đã được đặt.");

                $giuTam = DB::table('ghe_giu_tam')
                    ->where('suat_chieu_id',$suatChieuId)
                    ->where('ghe_id',$gheId)
                    ->where('het_han','>',Carbon::now())
                    ->exists();
                if($giuTam) throw new \Exception("Ghế đang được giữ tạm.");

                $status = GheSuatChieu::where('suat_chieu_id',$suatChieuId)
                    ->where('ghe_id',$gheId)->value('trang_thai');
                if(in_array($status,['bao_tri','vo_hieu_hoa'])) throw new \Exception("Ghế không khả dụng.");
            }

            DB::table('ghe_giu_tam')->where('nguoi_dung_id',auth()->id())->delete();

            $holdData = [];
            foreach($gheIds as $gheId){
                $holdData[] = [
                    'suat_chieu_id'=>$suatChieuId,
                    'ghe_id'=>$gheId,
                    'nguoi_dung_id'=>auth()->id(),
                    'het_han'=>Carbon::now()->addMinutes(10)
                ];
            }
            DB::table('ghe_giu_tam')->insert($holdData);

            DB::commit();
            return response()->json(['success'=>true,'message'=>'Đã giữ tạm ghế.']);
        } catch (\Exception $e){
            DB::rollback();
            return response()->json(['success'=>false,'message'=>$e->getMessage()]);
        }
    }

    // Đặt vé
    public function store(Request $request)
    {
        $request->validate([
            'suat_chieu_id'=>'required|exists:suat_chieu,id',
            'ghe_ids'=>'required|array|min:1|max:2',
            'ghe_ids.*'=>'exists:ghe,id',
            'combo_items'=>'nullable|array',
            'combo_items.*.combo_id'=>'exists:combo,id',
            'combo_items.*.so_luong'=>'integer|min:1',
            'ma_giam_gia'=>'nullable|string|exists:ma_giam_gia,ma',
        ]);

        if(!auth()->check()) return response()->json(['success'=>false,'message'=>'Đăng nhập để đặt vé.']);

        $suatChieu = SuatChieu::findOrFail($request->suat_chieu_id);
        $gheIds = $request->ghe_ids;
        $comboItems = $request->combo_items ?? [];
        $maGiamGia = $request->ma_giam_gia;

        DB::beginTransaction();
        try {
            // Tính tiền vé
            $tongTienVe = 0;
            $ghePrices = [];
            foreach($gheIds as $gheId){
                $ghe = Ghe::find($gheId);
                $price = $suatChieu->gia_ve;
                if($ghe->loai==='vip') $price*=1.5;
                elseif($ghe->loai==='doi') $price*=2;
                $ghePrices[$gheId]=$price;
                $tongTienVe+=$price;
            }

            // Tính tiền combo
            $tongTienCombo=0;
            $donDatVeCombos=[];
            foreach($comboItems as $item){
                $combo = Combo::find($item['combo_id']);
                $soLuong = $item['so_luong'];
                $tongTienCombo += $combo->gia * $soLuong;
                $donDatVeCombos[] = ['combo_id'=>$combo->id,'so_luong'=>$soLuong,'gia'=>$combo->gia];
            }

            $tongTien = $tongTienVe + $tongTienCombo;

            // Áp voucher
            $maGiamGiaObj = null;
            if($maGiamGia){
                $maGiamGiaObj = MaGiamGia::where('ma',$maGiamGia)
                    ->where('trang_thai','hoat_dong')
                    ->where('ngay_bat_dau','<=',Carbon::now())
                    ->where('ngay_ket_thuc','>=',Carbon::now())
                    ->first();
                if($maGiamGiaObj){
                    $giamGia = $maGiamGiaObj->loai==='phan_tram'
                        ? min($tongTien*($maGiamGiaObj->gia_tri/100), $maGiamGiaObj->giam_toi_da??$tongTien)
                        : $maGiamGiaObj->gia_tri;
                    $tongTien -= $giamGia;
                }
            }

            $donDatVe = DonDatVe::create([
                'ma_don'=>'DV'.time().rand(100,999),
                'nguoi_dung_id'=>auth()->id(),
                'suat_chieu_id'=>$suatChieu->id,
                'ma_giam_gia_id'=>$maGiamGiaObj->id??null,
                'tong_tien'=>$tongTien,
                'trang_thai'=>'cho_thanh_toan'
            ]);

            foreach($gheIds as $gheId){
                ChiTietVe::create([
                    'don_dat_ve_id'=>$donDatVe->id,
                    'suat_chieu_id'=>$suatChieu->id,
                    'ghe_id'=>$gheId,
                    'gia'=>$ghePrices[$gheId],
                    'loai_ghe'=>Ghe::find($gheId)->loai,
                    'trang_thai'=>'da_dat'
                ]);
            }

            foreach($donDatVeCombos as $comboData){
                DB::table('don_dat_ve_combo')->insert(array_merge($comboData,['don_dat_ve_id'=>$donDatVe->id]));
            }

            DB::table('ghe_giu_tam')->where('nguoi_dung_id',auth()->id())->delete();

            DB::commit();
            return response()->json(['success'=>true,'redirect'=>route('booking.payment',$donDatVe->id)]);
        } catch(\Exception $e){
            DB::rollback();
            return response()->json(['success'=>false,'message'=>$e->getMessage()]);
        }
    }

    // Kiểm tra voucher
    public function checkVoucher(Request $request)
    {
        $request->validate([
            'code'=>'required|string',
            'suat_chieu_id'=>'required|exists:suat_chieu,id',
            'ghe_ids'=>'required|array|min:1|max:2',
            'ghe_ids.*'=>'exists:ghe,id',
            'combo_items'=>'nullable|array',
            'combo_items.*.combo_id'=>'exists:combo,id',
            'combo_items.*.so_luong'=>'integer|min:1',
        ]);

        $maGiamGia = MaGiamGia::where('ma',$request->code)
            ->where('trang_thai','hoat_dong')
            ->where('ngay_bat_dau','<=',Carbon::now())
            ->where('ngay_ket_thuc','>=',Carbon::now())
            ->first();

        if(!$maGiamGia) return response()->json(['success'=>false,'message'=>'Voucher không hợp lệ']);

        $suatChieu = SuatChieu::findOrFail($request->suat_chieu_id);
        $tongTienVe = count($request->ghe_ids)*$suatChieu->gia_ve;
        $tongTienCombo=0;
        foreach($request->combo_items??[] as $item){
            $combo = Combo::find($item['combo_id']);
            $tongTienCombo += $combo->gia*$item['so_luong'];
        }
        $tongTien = $tongTienVe + $tongTienCombo;

        $giamGia = $maGiamGia->loai==='phan_tram'
            ? min($tongTien*($maGiamGia->gia_tri/100), $maGiamGia->giam_toi_da??$tongTien)
            : $maGiamGia->gia_tri;

        return response()->json(['success'=>true,'discount'=>$giamGia]);
    }
}
