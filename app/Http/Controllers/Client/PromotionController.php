<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Store;
use App\Services\PromotionService;
use Illuminate\Http\Request;

/**
 * Client controller for browsing and searching promotions.
 * Handles promotion discovery and filtering by store, category, date.
 */
class PromotionController extends Controller
{
    public function __construct(
        private PromotionService $promotionService
    ) {
    }

    /**
     * Display a listing of available promotions for the authenticated client.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['store_id', 'search']);

        $promotions = $this->promotionService->getAvailablePromotions(
            auth()->user(),
            $filters
        );

        $stores = Store::orderBy('nombre')->get();

        $clientCategory = auth()->user()->categoria_cliente;

        return view('client.promotions.index', compact('promotions', 'stores', 'clientCategory'));
    }

    /**
     * Display the specified promotion with eligibility check.
     */
    public function show(Promotion $promotion)
    {
        $promotion->load('store');

        // Check eligibility for this client
        $eligibility = $this->promotionService->checkEligibility($promotion, auth()->user());

        // Check if client has already requested this promotion
        $hasRequested = $promotion->usages()
            ->where('client_id', auth()->id())
            ->exists();

        return view('client.promotions.show', compact('promotion', 'eligibility', 'hasRequested'));
    }

    /**
     * Display promotions by store.
     */
    public function byStore(Store $store)
    {
        $filters = ['store_id' => $store->id];

        $promotions = $this->promotionService->getAvailablePromotions(
            auth()->user(),
            $filters
        );

        return view('client.promotions.by-store', compact('store', 'promotions'));
    }
}
