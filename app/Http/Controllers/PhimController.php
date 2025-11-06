<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DanhMuc;
use App\Models\Phim;
use App\Models\TheLoai;
use App\Models\Rap;

class PhimController extends Controller
{
    /**
     * Hiển thị danh sách phim theo danh mục và hỗ trợ filter (the_loai, rap, date)
     */
    public function category(Request $request, $slug)
    {
        $danhMuc = DanhMuc::where('slug', $slug)->firstOrFail();

        // Normalize / sanitize query params to avoid accidental empty-string filters
        $theLoaiRaw = trim((string) $request->query('the_loai', ''));
        $rapRaw = trim((string) $request->query('rap', ''));
        $dateRaw = trim((string) $request->query('date', ''));

        $theLoaiId = $theLoaiRaw === '' ? null : (is_numeric($theLoaiRaw) ? (int) $theLoaiRaw : null);
        $rapId = $rapRaw === '' ? null : (is_numeric($rapRaw) ? (int) $rapRaw : null);

        // Validate date (expecting Y-m-d). If invalid, ignore it.
        $date = null;
        if ($dateRaw !== '') {
            try {
                $dt = \Carbon\Carbon::createFromFormat('Y-m-d', $dateRaw);
                $date = $dt->format('Y-m-d');
            } catch (\Exception $e) {
                $date = null;
            }
        }

        $query = $danhMuc->phims()->with(['theLoais', 'danhMuc']);

        // Lọc theo thể loại (the_loai id)
        if ($theLoaiId) {
            $query->whereHas('theLoais', function($q) use ($theLoaiId) {
                $q->where('id', $theLoaiId);
            });
        }

        // Lọc theo rạp (rap id) và/hoặc ngày chiếu.
        // Nếu có chọn rạp: muốn phim có suất chiếu tại rạp đó (và theo ngày nếu cung cấp).
        // Đồng thời nếu chỉ chọn ngày (không chọn rạp), người dùng thường muốn thấy phim
        // có `ngay_cong_chieu` trùng ngày đó.
        if ($rapId || $date) {
            $query->where(function($q) use ($rapId, $date) {
                if ($rapId) {
                    $q->whereHas('suatChieus', function($sq) use ($rapId, $date) {
                        $sq->whereHas('phong', function($pq) use ($rapId) {
                            $pq->where('rap_id', $rapId);
                        });

                        if ($date) {
                            $sq->whereDate('gio_bat_dau', $date);
                        }
                    });
                }

                if ($date) {
                    // OR: phim có ngay_cong_chieu bằng ngày tìm kiếm
                    $q->orWhereDate('ngay_cong_chieu', $date);
                }
            });
        }

        $movies = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        // Danh sách thể loại và rạp để hiển thị bộ lọc
        $theLoais = TheLoai::orderBy('ten')->get();
        $raps = Rap::orderBy('ten')->get();

        // DEBUG helper: nếu URL có ?debug=1 thì in ra dữ liệu và SQL để kiểm tra
        if ($request->filled('debug')) {
            // dd the results and the generated SQL for the movies query
            return dd([
                'theLoais' => $theLoais->toArray(),
                'raps' => $raps->toArray(),
                'movies_sql' => $query->toSql(),
                'movies_bindings' => $query->getBindings(),
            ]);
        }

        return view('client.movies.category', compact('danhMuc', 'movies', 'theLoais', 'raps'));
    }

    /**
     * Hiển thị chi tiết phim theo slug
     */
    public function show($slug)
    {
        $phim = Phim::where('slug', $slug)->with(['theLoais', 'danhMuc'])->firstOrFail();

        return view('client.movies.show', compact('phim'));
    }
}
