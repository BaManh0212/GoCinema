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

    protected function generateQrCode()
    {
        $data = [
            'ma_don' => $this->donDatVe->ma_don,
            'ngay_dat' => now()->format('Y-m-d H:i:s'),
            'phim' => $this->donDatVe->suatChieu->phim->tieu_de ?? 'N/A',
            'rap' => $this->donDatVe->suatChieu->phong->rap->ten ?? 'N/A',
            'phong' => $this->donDatVe->suatChieu->phong->ten ?? 'N/A',
            'ngay_chieu' => $this->donDatVe->suatChieu->ngay_chieu ?? 'N/A',
            'gio_bat_dau' => $this->donDatVe->suatChieu->gio_bat_dau ?? 'N/A',
            'tong_tien' => number_format($this->donDatVe->tong_tien, 0, ',', '.') . ' VNĐ',
            'trang_thai' => $this->donDatVe->trang_thai === 'da_thanh_toan' ? 'Đã thanh toán' : 'Chờ thanh toán'
        ];

        $qrText = json_encode($data);

        try {
            // 1) Nếu Endroid\QrCode có sẵn -> dùng Endroid writer (PNG)
            if (class_exists(\Endroid\QrCode\Writer\PngWriter::class) && class_exists(\Endroid\QrCode\QrCode::class)) {
                $qr = \Endroid\QrCode\QrCode::create($qrText)
                    ->setEncoding(new \Endroid\QrCode\Encoding\Encoding('UTF-8'))
                    ->setSize(300);
                $writer = new \Endroid\QrCode\Writer\PngWriter();
                $result = $writer->write($qr);
                $pngData = $result->getString();
            }
            // 2) Fallback: nếu có BaconQrCode -> render PNG
            elseif (class_exists(\BaconQrCode\Renderer\Image\PngImageBackEnd::class) &&
                    class_exists(\BaconQrCode\Writer::class)) {
                $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                    new \BaconQrCode\Renderer\RendererStyle\RendererStyle(300),
                    new \BaconQrCode\Renderer\Image\PngImageBackEnd()
                );
                $writer = new \BaconQrCode\Writer($renderer);
                $pngData = $writer->writeString($qrText); // binary PNG
            } else {
                throw new \Exception('No QR library (Endroid or Bacon) available.');
            }

            // Lưu binary và data-uri
            $this->pngData = $pngData ?? null;
            $this->qrCode = $this->pngData ? 'data:image/png;base64,' . base64_encode($this->pngData) : null;
        } catch (\Throwable $e) {
            \Log::error('[BookingConfirmation] QR generation failed: ' . $e->getMessage());
            $this->pngData = null;
            $this->qrCode = null;
        }
    }

    public function build()
    {
        $mail = $this->subject('Xác nhận đặt vé thành công - ' . $this->donDatVe->ma_don . ' - ' . config('app.name'))
                   ->view('emails.booking.confirmation');

        // Nếu có png data, attach để sử dụng CID (đảm bảo hiển thị tốt trên mail client)
        if (!empty($this->pngData)) {
            $mail->attachData($this->pngData, 'qrcode.png', ['mime' => 'image/png']);
            // Set qrCode to cid reference so view can use <img src="{{ $qrCode }}">
            $this->qrCode = 'cid:qrcode.png';
        }

        return $mail;
    }
}