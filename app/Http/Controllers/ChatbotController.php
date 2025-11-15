<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\Showtime;
use App\Models\Combo;
use App\Models\Baiviets;
use App\Models\DonDatVe;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        $messageLower = mb_strtolower($message);
        $reply = "";

        try {
            // 1. Hỏi lịch chiếu hôm nay
            if (str_contains($messageLower, 'hôm nay')) {
                $today = now()->toDateString();
                $showtimes = Showtime::with('movie')
                    ->whereDate('start_time', $today)
                    ->get();

                if ($showtimes->isNotEmpty()) {
                    $reply = "🎬 Suất chiếu hôm nay:\n";
                    foreach ($showtimes as $s) {
                        $reply .= "- {$s->movie->title} lúc {$s->start_time->format('H:i')} giá {$s->price}đ (Phòng {$s->room})\n";
                    }
                } else {
                    $reply = "Hôm nay chưa có suất chiếu nào.";
                }
            }

            // 2. Hỏi lịch chiếu ngày mai
            elseif (str_contains($messageLower, 'ngày mai')) {
                $tomorrow = Carbon::tomorrow()->toDateString();
                $showtimes = Showtime::with('movie')
                    ->whereDate('start_time', $tomorrow)
                    ->get();

                if ($showtimes->isNotEmpty()) {
                    $reply = "📅 Suất chiếu ngày mai:\n";
                    foreach ($showtimes as $s) {
                        $reply .= "- {$s->movie->title} lúc {$s->start_time->format('H:i')} giá {$s->price}đ (Phòng {$s->room})\n";
                    }
                } else {
                    $reply = "Ngày mai chưa có suất chiếu nào.";
                }
            }

            // 3. Hỏi combo
            elseif (str_contains($messageLower, 'combo')) {
                $combos = Combo::all();
                if ($combos->isNotEmpty()) {
                    $reply = "🍿 Các combo hiện có:\n";
                    foreach ($combos as $c) {
                        $reply .= "- {$c->name}: {$c->description} (Giá {$c->price}đ)\n";
                    }
                } else {
                    $reply = "Hiện chưa có combo nào.";
                }
            }

            // 4. Hỏi chi tiết phim/bài viết hoặc đánh giá
            elseif (str_contains($messageLower, 'phim') || str_contains($messageLower, 'bài viết')) {
                if (str_contains($messageLower, 'có hay không')) {
                    $movie = Movie::where('title', 'like', '%' . $message . '%')->first();
                    if ($movie) {
                        $rating = $movie->rating ?? 4.0;
                        $reply = "🎬 Phim {$movie->title} rất hay, được đánh giá {$rating} sao trên 5.";
                    } else {
                        $reply = "Xin lỗi, tôi chưa tìm thấy phim bạn hỏi.";
                    }
                } else {
                    $baiviet = Baiviets::where('tieu_de', 'like', '%' . $message . '%')->first();
                    if ($baiviet) {
                        $reply = "🎬 {$baiviet->tieu_de}: {$baiviet->tom_tat}";
                    } else {
                        $titles = Baiviets::pluck('tieu_de')->implode(", ");
                        $reply = $titles
                            ? "📚 Các bài viết/phim hiện có: " . $titles
                            : "Hiện chưa có dữ liệu.";
                    }
                }
            }

            // 5. Đặt vé
            elseif (str_contains($messageLower, 'đặt vé')) {
                preg_match('/\b(\d{1,2})h(\d{0,2})?\b/', $messageLower, $matches);
                $hour = $matches[1] ?? null;

                $movie = Movie::where('title', 'like', '%' . $message . '%')->first();

                if ($movie && $hour) {
                    $showtime = Showtime::where('movie_id', $movie->id)
                        ->whereTime('start_time', $hour . ':00:00')
                        ->first();

                    if ($showtime) {
                        session([
                            'suat_chieu_id' => $showtime->id,
                            'movie_title' => $movie->title,
                            'price' => $showtime->price,
                        ]);

                        $reply = "🎬 Bạn muốn đặt vé cho phim {$movie->title} lúc {$showtime->start_time->format('H:i')} (Phòng {$showtime->room}, giá {$showtime->price}đ/vé). Vui lòng cho biết số lượng vé.";
                    } else {
                        $reply = "Xin lỗi, chưa có suất chiếu nào lúc {$hour}h cho phim {$movie->title}.";
                    }
                } else {
                    if ($movie) {
                        session(['movie_title' => $movie->title]);
                        $reply = "Phim {$movie->title} hôm nay còn suất chiếu mấy giờ?";
                    } else {
                        $reply = "Bạn muốn đặt vé cho phim nào và vào lúc mấy giờ?";
                    }
                }
            }

            // 6. Người dùng hỏi "còn suất chiếu mấy giờ"
            elseif (str_contains($messageLower, 'suất chiếu') || str_contains($messageLower, 'mấy giờ')) {
                $movieTitle = session('movie_title');
                if ($movieTitle) {
                    $movie = Movie::where('title', 'like', '%' . $movieTitle . '%')->first();
                    if ($movie) {
                        $showtimes = Showtime::where('movie_id', $movie->id)
                            ->whereDate('start_time', now()->toDateString())
                            ->get();

                        if ($showtimes->isNotEmpty()) {
                            $list = $showtimes->map(fn($s) => $s->start_time->format('H:i'))->implode(", ");
                            $reply = "🎬 Phim {$movie->title} hôm nay còn các suất chiếu: {$list}";
                        } else {
                            $reply = "Phim {$movie->title} hôm nay không còn suất chiếu nào.";
                        }
                    } else {
                        $reply = "Xin lỗi, tôi chưa tìm thấy phim bạn hỏi.";
                    }
                } else {
                    $reply = "Bạn muốn xem suất chiếu của phim nào?";
                }
            }

            // 7. Người dùng nhập số lượng vé
            elseif (preg_match('/(\d+)\s*v[ée]/', $messageLower, $matches)) {
                $soLuong = (int)$matches[1];
                $suatChieuId = session('suat_chieu_id');
                $movieTitle = session('movie_title');
                $price = session('price');

                if ($suatChieuId && $soLuong) {
                    $tongTien = $soLuong * $price;

                    $donDatVe = DonDatVe::create([
                        'ma_don' => 'MDV' . time(),
                        'nguoi_dung_id' => null,
                        'suat_chieu_id' => $suatChieuId,
                        'ma_giam_gia_id' => null,
                        'tong_tien' => $tongTien,
                        'trang_thai' => 'pending',
                    ]);

                    $reply = "✅ Bạn đã đặt {$soLuong} vé cho phim {$movieTitle}. Tổng tiền: {$tongTien}đ. Mã đơn: {$donDatVe->ma_don}";
                } else {
                    $reply = "Xin lỗi, tôi chưa có thông tin suất chiếu để đặt vé.";
                }
            }

            // 8. Hỏi trạng thái đơn đặt vé
            elseif (preg_match('/đơn\s+#?(MDV\d+)/', $message, $matches)) {
                $maDon = $matches[1];
                $don = DonDatVe::where('ma_don', $maDon)->first();

                if ($don) {
                    $reply = "📄 Đơn {$maDon}: Phim {$don->suatChieu->movie->title}, Tổng tiền {$don->tong_tien}đ, Trạng thái {$don->trang_thai}.";
                } else {
                    $reply = "Không tìm thấy đơn {$maDon}.";
                }
            }

            elseif (str_contains($messageLower, 'có hay không')) {
    $movie = Movie::where('title', 'like', '%' . $message . '%')->first();
    if ($movie) {
        $rating = $movie->rating ?? 4.0;

        // Lấy suất chiếu hôm nay của phim
        $today = now()->toDateString();
        $showtimes = Showtime::where('movie_id', $movie->id)
            ->whereDate('start_time', $today)
            ->pluck('id');

        // Đếm số đơn đặt vé cho các suất chiếu đó
        $countBookings = DonDatVe::whereIn('suat_chieu_id', $showtimes)->count();

        if ($countBookings > 0) {
            $reply = "🎬 Phim {$movie->title} rất hay, được đánh giá {$rating} sao trên 5 và đã có {$countBookings} suất chiếu được đặt vào hôm nay.";
        } else {
            $reply = "🎬 Phim {$movie->title} rất hay, được đánh giá {$rating} sao trên 5. Hôm nay chưa có suất chiếu nào được đặt.";
        }
    } else {
        $reply = "Xin lỗi, tôi chưa tìm thấy phim bạn hỏi.";
    }
}

elseif (str_contains($messageLower, 'có hay không')) {
    $movie = Movie::where('title', 'like', '%' . $message . '%')->first();
    if ($movie) {
        $rating = $movie->rating ?? 4.0;

        // Lấy suất chiếu hôm nay của phim
        $today = now()->toDateString();
        $showtimes = Showtime::where('movie_id', $movie->id)
            ->whereDate('start_time', $today)
            ->pluck('id');

        // Đếm số đơn đặt vé cho các suất chiếu đó
        $countBookings = DonDatVe::whereIn('suat_chieu_id', $showtimes)->count();

        if ($countBookings > 0) {
            $reply = "🎬 Phim {$movie->title} rất hay, được đánh giá {$rating} sao trên 5 và đã có {$countBookings} suất chiếu được đặt vào hôm nay.";
        } else {
            $reply = "🎬 Phim {$movie->title} rất hay, được đánh giá {$rating} sao trên 5. Hôm nay chưa có suất chiếu nào được đặt.";
        }
    } else {
        $reply = "Xin lỗi, tôi chưa tìm thấy phim bạn hỏi.";
    }
}
            // 9. Fallback
            else {
                $reply = "Xin lỗi, tôi chưa hiểu câu hỏi. Bạn có thể hỏi về lịch chiếu, combo, chi tiết phim hoặc đặt vé nhé.";
            }

            return response()->json(['reply' => $reply], 200);

        } catch (\Exception $e) {
            return response()->json([
                'reply' => 'Xin lỗi, hệ thống đang gặp sự cố. Vui lòng thử lại sau 🙏'
            ], 500);
        }
    }
}
