<?php

namespace App\Http\Controllers;

use App\Models\HomepageSection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $heroSlides = HomepageSection::carouselSlides();
        $popupOffer = HomepageSection::activePopupOffer();

        return view('index', [
            'heroSlides' => $heroSlides,
            'popupOffer' => $popupOffer,
        ]);
    }
}
