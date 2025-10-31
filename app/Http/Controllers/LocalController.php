<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LocalController extends Controller
{
    /**
     * Display a listing of all stores
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // TODO: Implement filtering by category, letter, search query
        // TODO: Fetch stores from database with pagination
        // For now, the view uses mock data embedded in the Blade template
        
        return view('pages.locales.index');
    }

    /**
     * Display the specified store details
     *
     * @param int $id Store ID
     * @return View
     */
    public function show(int $id): View
    {
        // TODO: Fetch store by ID from database
        // TODO: Load related promotions for this store
        // TODO: Handle 404 if store not found
        // For now, the view uses mock data with the provided ID
        
        return view('pages.locales.show', ['storeId' => $id]);
    }
}
