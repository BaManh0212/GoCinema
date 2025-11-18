<?php

namespace App\Mail;

use App\Models\DonDatVe;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookingConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $donDatVe;
    public $qrCode;
    public $totalTicketPrice;

    public function __construct(DonDatVe $donDatVe)
    {
        $this->donDatVe = $donDatVe->load(['suatChieu.phim', 'suatChieu.phong.rap', 'chiTietVes.ghe', 'maGiamGia']);
        $this->calculateTotalTicketPrice();
        $this->generateQrCode();
    }

    protected function calculateTotalTicketPrice()
    {
        $this->totalTicketPrice = 0;
        foreach ($this->donDatVe->chiTietVes as $ve) {
            $seatPrice = $this->donDatVe->suatChieu->gia_ve;
            if ($ve->ghe->loai === 'vip') {
                $seatPrice *= 1.5;
            } elseif ($ve->ghe->loai === 'doi') {
                $seatPrice *= 2;
            }
            $this->totalTicketPrice += $seatPrice;
        }
    }

    protected function generateQrCode()
    {
        $data = [
            'ma_don' => $this->donDatVe->ma_don,
            'nguoi_dat' => $this->donDatVe->nguoiDung ? $this->donDatVe->nguoiDung->ho_ten : 'Khách hàng',
            'phim' => $this->donDatVe->suatChieu->phim->tieu_de,
            'rap' => $this->donDatVe->suatChieu->phong->rap->ten,
            'phong' => $this->donDatVe->suatChieu->phong->ten,
            'ngay_chieu' => $this->donDatVe->suatChieu->gio_bat_dau->format('d/m/Y H:i'),
            'tong_tien' => number_format($this->donDatVe->tong_tien, 0, ',', '.') . ' VNĐ',
            'trang_thai' => $this->donDatVe->trang_thai === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chờ thanh toán'
        ];
        
        $this->qrCode = base64_encode(QrCode::format('png')
            ->size(300)
            ->generate(json_encode($data)));
    }

    public function build()
    {
        return $this->subject('Xác nhận đặt vé thành công - ' . $this->donDatVe->ma_don . ' - ' . config('app.name'))
                   ->view('emails.booking.confirmation');
    }
}
