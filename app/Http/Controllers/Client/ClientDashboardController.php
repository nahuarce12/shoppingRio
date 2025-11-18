<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientDashboardController extends Controller
{
    /**
     * Display the client dashboard
     *
     * @return View
     */
    public function index(): View
    {
        // TODO: Implement authentication check (verified client only)
        // TODO: Fetch authenticated client's information:
        //   - Current category (Inicial, Medium, Premium)
        //   - Category upgrade eligibility
        //   - Total promotions used
        // TODO: Fetch client-specific data for dashboard sections:
        //   - Personal information and profile
        //   - Available promotions (filtered by category)
        //   - Usage history (promotions requested/redeemed)
        //   - Category-targeted news/announcements
        // For now, the view uses mock data embedded in the Blade template
        
        return view('dashboard.client.index');
    }
}
