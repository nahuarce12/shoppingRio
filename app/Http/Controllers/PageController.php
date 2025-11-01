<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Display the "About Us" page
     *
     * @return View
     */
    public function about(): View
    {
        // TODO: Fetch dynamic content from database if needed
        // For now, content is static in the Blade template
        
        return view('pages.static.about');
    }

    /**
     * Display the contact page
     *
     * @return View
     */
    public function contact(): View
    {
        // TODO: Fetch contact information from settings/database
        // For now, content is static in the Blade template
        
        return view('pages.static.contact');
    }

    /**
     * Handle contact form submission
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitContact(Request $request)
    {
        // TODO: Validate contact form
        // TODO: Send email to administrator
        // TODO: Store message in database
        // TODO: Return success/error response
        
        return back()->with('success', 'Mensaje enviado correctamente. Te contactaremos pronto.');
    }
}
