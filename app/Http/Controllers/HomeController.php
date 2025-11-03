<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phim;

class HomeController extends Controller
{
    /**
     * Display the client home with featured movies.
     */
    public function index(Request $request)
    {
        // Featured: currently showing, ordered by views
        $featured = Phim::dangChieu()->orderByDesc('luot_xem')->limit(8)->get();

        // Choose banner: first featured with a banner image
        $bannerFilm = Phim::dangChieu()->whereNotNull('banner')->orderByDesc('luot_xem')->first();
        $banner = $bannerFilm?->banner ? asset('uploads/' . $bannerFilm->banner) : null;

        return view('client.home', compact('featured', 'banner'));
    }
}
