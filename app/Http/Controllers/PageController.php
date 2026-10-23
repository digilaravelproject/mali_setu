<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
    public function privacyPolicy()
    {
        $page = Page::where('page_type', 'privacy_policy')
                    ->where('status', 1)
                    ->first();

        return view('privacy-policy', compact('page'));
    }

    public function termsCondition()
    {
        $page = Page::where('page_type', 'terms_condition')
                    ->where('status', 1)
                    ->first();

        return view('terms-condition', compact('page'));
    }

    public function contactUs()
    {
        $page = Page::where('page_type', 'contact_us')
                    ->where('status', 1)
                    ->first();

        return view('contact-us', compact('page'));
    }
}