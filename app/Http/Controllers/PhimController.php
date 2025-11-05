<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DanhMuc;

class PhimController extends Controller
{
    public function category($slug)
    {
        $danhMuc = DanhMuc::where('slug', $slug)->firstOrFail();

        // Lấy danh sách phim qua quan hệ many-to-many
        $movies = $danhMuc->phims()->paginate(12);

        return view('client.movies.category', compact('danhMuc', 'movies'));
    }
}
