<?php
namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show(): \Illuminate\View\View
    {
        $navPages = Page::published()->inNav()->get();
        $contactPage = Page::published()->where('slug', 'contact')->with('translations')->first();
        return view('public.contact', compact('navPages', 'contactPage'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:3000',
        ]);

        ContactMessage::create($data);

        try {
            $adminEmail = \App\Models\Setting::get('admin_email', config('mail.from.address'));
            if ($adminEmail) {
                Mail::raw(
                    "From: {$data['name']} <{$data['email']}>\nSubject: {$data['subject']}\n\n{$data['message']}",
                    fn ($m) => $m->to($adminEmail)->subject("Contact: {$data['subject']}")
                );
            }
        } catch (\Throwable) {}

        return back()->with('contact_success', true);
    }
}