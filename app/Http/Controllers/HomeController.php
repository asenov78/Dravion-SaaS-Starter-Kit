<?php
namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $navPages = Page::published()->inNav()->get();
        $homePage = Page::published()->where('slug', 'home')->first();
        return view('public.home', compact('navPages', 'homePage'));
    }

    public function show(string $slug): View|\Illuminate\Http\RedirectResponse
    {
        $navPages = Page::published()->inNav()->get();
        $page = Page::published()->where('slug', $slug)->firstOrFail();
        return view('public.page', compact('navPages', 'page'));
    }
}