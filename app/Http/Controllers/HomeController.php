<?php
namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $navPages = Page::published()->inNav()->get();
        $homePage = Page::published()->where('slug', 'home')->with('translations')->first();
        return view('public.home', compact('navPages', 'homePage'));
    }

    public function show(string $slug): View|\Illuminate\Http\RedirectResponse
    {
        $navPages = Page::published()->inNav()->get();
        $page = Page::published()->where('slug', $slug)->with('translations')->firstOrFail();
        return view('public.page', compact('navPages', 'page'));
    }

    public function gallery(): View
    {
        $navPages = Page::published()->inNav()->get();
        $galleryPage = Page::published()->where('slug', 'gallery')->with('translations')->first();
        return view('public.gallery', compact('navPages', 'galleryPage'));
    }
}