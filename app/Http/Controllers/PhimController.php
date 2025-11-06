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

        $query = $danhMuc->phims()->with(['theLoais', 'danhMuc']);

        // Lọc theo thể loại (the_loai id)
        if ($request->filled('the_loai')) {
            $theLoaiId = $request->get('the_loai');
            $query->whereHas('theLoais', function($q) use ($theLoaiId) {
                $q->where('the_loai.id', $theLoaiId)->orWhere('the_loai_id', $theLoaiId);
            });
        }

        // Lọc theo rạp (rap id) và/hoặc ngày chiếu
        if ($request->filled('rap') || $request->filled('date')) {
            $rapId = $request->get('rap');
            $date = $request->get('date');

            $query->whereHas('suatChieus', function($q) use ($rapId, $date) {
                if ($rapId) {
                    $q->whereHas('phong', function($q2) use ($rapId) {
                        $q2->where('rap_id', $rapId);
                    });
                }
                if ($date) {
                    // so sánh theo ngày của gio_bat_dau
                    $q->whereDate('gio_bat_dau', $date);
                }
            });
        }

        $movies = $query->orderByDesc('created_at')->paginate(12)->withQueryString();

        // Danh sách thể loại và rạp để hiển thị bộ lọc
        $theLoais = TheLoai::orderBy('ten')->get();
        $raps = Rap::orderBy('ten')->get();

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
