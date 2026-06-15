<?php

namespace App\Http\Controllers;

use App\Models\Language;

class LocaleController extends Controller
{
    public function switch(string $code)
    {
        if (Language::where('code', $code)->exists()) {
            session(['locale' => $code]);
        }

        return redirect()->back();
    }
}
