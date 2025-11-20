<?php

namespace App\Mail;

use App\Models\DonDatVe;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
            $this->totalTicketPrice += $ve->calculated_price;
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

        // Tạo mã QR sử dụng endroid/qr-code
        $qrCode = QrCode::create(json_encode($data))
            ->setEncoding(new Encoding('UTF-8'))
            ->setSize(300)
            ->setMargin(10)
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High);

        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        // Lấy dữ liệu base64 của ảnh
        $this->qrCode = base64_encode($result->getString());
    }

    public function build()
    {
        return $this->subject('Xác nhận đặt vé thành công - ' . $this->donDatVe->ma_don . ' - ' . config('app.name'))
                   ->view('emails.booking.confirmation');
    }
}