<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Store;
use App\Services\PromotionService;
use Illuminate\Http\Request;

/**
 * Public controller for unregistered users.
 * Provides access to all promotions and stores without authentication.
 * Per business rules, unregistered users can view all content but cannot request promotions.
 */
class PublicController extends Controller
{
    public function __construct(
        private PromotionService $promotionService
    ) {
    }

    /**
     * Display the home page.
     */
    public function home()
    {
        // Featured promotions (recent approved promotions)
        $featuredPromotions = Promotion::approved()
            ->active()
            ->with('store')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Total counts for stats
        $stats = [
            'stores' => Store::count(),
            'active_promotions' => Promotion::approved()->active()->count(),
            'categories' => ['Inicial', 'Medium', 'Premium'],
        ];

        return view('public.home', compact('featuredPromotions', 'stats'));
    }

    /**
     * Display all available promotions with filters.
     */
    public function promotions(Request $request)
    {
        $filters = $request->only(['store_id', 'categoria_minima', 'search']);

        $promotions = $this->promotionService->getPublicPromotions($filters);

        $stores = Store::orderBy('nombre')->get();
        $categories = ['Inicial', 'Medium', 'Premium'];

        return view('public.promotions', compact('promotions', 'stores', 'categories'));
    }

    /**
     * Display a single promotion.
     */
    public function showPromotion(Promotion $promotion)
    {
        // Only show approved promotions to public
        if ($promotion->estado !== 'aprobada') {
            abort(404);
        }

        $promotion->load('store');

        // Show similar promotions from same store
        $similarPromotions = Promotion::approved()
            ->active()
            ->where('store_id', $promotion->store_id)
            ->where('id', '!=', $promotion->id)
            ->limit(3)
            ->get();

        return view('public.promotion-show', compact('promotion', 'similarPromotions'));
    }

    /**
     * Display all stores.
     */
    public function stores(Request $request)
    {
        $query = Store::query();

        // Filter by rubro
        if ($request->filled('rubro')) {
            $query->where('rubro', $request->rubro);
        }

        // Search by name or location
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('ubicacion', 'like', "%{$search}%");
            });
        }

        $query->orderBy('nombre');

        $stores = $query->paginate(12);

        $rubros = config('shopping.store_rubros', []);

        return view('public.stores', compact('stores', 'rubros'));
    }

    /**
     * Display a single store with its promotions.
     */
    public function showStore(Store $store)
    {
        $store->load(['owner', 'promotions' => function ($query) {
            $query->approved()->active()->orderBy('created_at', 'desc');
        }]);

        $promotionCount = $store->promotions()->approved()->active()->count();

        return view('public.store-show', compact('store', 'promotionCount'));
    }

    /**
     * Display contact information.
     */
    public function contact()
    {
        $contactInfo = config('shopping.admin_contact', []);

        return view('public.contact', compact('contactInfo'));
    }

    /**
     * Display about page.
     */
    public function about()
    {
        $benefits = [
            'Inicial' => config('shopping.client_categories.Inicial.benefits', []),
            'Medium' => config('shopping.client_categories.Medium.benefits', []),
            'Premium' => config('shopping.client_categories.Premium.benefits', []),
        ];

        return view('public.about', compact('benefits'));
    }
}
