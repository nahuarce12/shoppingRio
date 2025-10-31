<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the home page with featured promotions and stores
     *
     * @return View
     */
    public function index(): View
    {
        // TODO: Fetch featured promotions and stores from database
        // For now, the view uses mock data embedded in the Blade template
        
        return view('home.index');
    }
}
