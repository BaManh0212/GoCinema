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
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\Image\ImageBackEndInterface;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
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

    protected function generateQrCode(){
    $data = [
        'ma_don' => $this->donDatVe->ma_don,
        'ngay_dat' => now()->format('Y-m-d H:i:s'),
        'phim' => $this->donDatVe->suatChieu->phim->ten_phim ?? 'N/A',
        'rap' => $this->donDatVe->suatChieu->phongChieu->rap->ten_rap ?? 'N/A',
        'phong' => $this->donDatVe->suatChieu->phongChieu->ten_phong ?? 'N/A',
        'ngay_chieu' => $this->donDatVe->suatChieu->ngay_chieu ?? 'N/A',
        'gio_bat_dau' => $this->donDatVe->suatChieu->gio_bat_dau ?? 'N/A',
        'tong_tien' => number_format($this->donDatVe->tong_tien, 0, ',', '.') . ' VNĐ',
        'trang_thai' => $this->donDatVe->trang_thai === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chờ thanh toán'
    ];

    // Use the same QR code generation as in admin print
    $renderer = new ImageRenderer(
        new RendererStyle(200, 1), // size=200, margin=1
        new SvgImageBackEnd()
    );
    $writer = new Writer($renderer);
    
    // Generate QR code as SVG
    $qrSvg = $writer->writeString(json_encode($data));
    
    // Store the SVG directly instead of base64 encoding
    $this->qrCode = $qrSvg;
}

    public function build()
    {
        return $this->subject('Xác nhận đặt vé thành công - ' . $this->donDatVe->ma_don . ' - ' . config('app.name'))
                   ->view('emails.booking.confirmation');
    }
}