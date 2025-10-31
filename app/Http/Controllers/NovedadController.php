<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class NovedadController extends Controller
{
    /**
     * Display a listing of active news/announcements
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // TODO: Fetch non-expired news from database
        // TODO: Filter by client category if authenticated
        //   - 'Inicial' news: visible to all
        //   - 'Medium' news: visible to Medium + Premium
        //   - 'Premium' news: visible only to Premium
        // TODO: Order by creation date DESC
        // For now, the view uses mock data embedded in the Blade template
        
        return view('pages.novedades.index');
    }
}
