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

        // Featured stores (stores with most active promotions)
        $featuredStores = Store::withCount(['promotions' => function ($query) {
                $query->approved()->active();
            }])
            ->having('promotions_count', '>', 0)
            ->orderBy('promotions_count', 'desc')
            ->limit(6)
            ->get();

        // Total counts for stats
        $stats = [
            'stores' => Store::count(),
            'active_promotions' => Promotion::approved()->active()->count(),
            'categories' => ['Inicial', 'Medium', 'Premium'],
        ];

        return view('home.index', compact('featuredPromotions', 'featuredStores', 'stats'));
    }

    /**
     * Display all available promotions with filters.
     */
    public function promotionsIndex(Request $request)
    {
        $filters = $request->only(['store_id', 'categoria_minima', 'search']);

        $query = Promotion::approved()->active()->with('store');

        // Filter by store
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        // Filter by minimum category
        if ($request->filled('categoria_minima')) {
            $query->where('categoria_minima', $request->categoria_minima);
        }

        // Search by text
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('texto', 'like', "%{$search}%");
        }

        $promotions = $query->orderBy('created_at', 'desc')->paginate(12);

        $stores = Store::orderBy('nombre')->get();
        $categories = ['Inicial', 'Medium', 'Premium'];

        return view('pages.promociones.index', compact('promotions', 'stores', 'categories'));
    }

    /**
     * Display a single promotion.
     */
    public function promotionShow(Promotion $promotion)
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

        return view('pages.promociones.show', compact('promotion', 'similarPromotions'));
    }

    /**
     * Display all stores.
     */
    public function storesIndex(Request $request)
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

        $rubros = ['Indumentaria', 'Tecnología', 'Gastronomía', 'Perfumería', 'Deportes', 'Hogar', 'Entretenimiento', 'Salud'];

        return view('pages.locales.index', compact('stores', 'rubros'));
    }

    /**
     * Display a single store with its promotions.
     */
    public function storeShow(Store $store)
    {
        $store->load(['owner', 'promotions' => function ($query) {
            $query->approved()->active()->orderBy('created_at', 'desc');
        }]);

        $promotionCount = $store->promotions()->approved()->active()->count();

        return view('pages.locales.show', compact('store', 'promotionCount'));
    }

    /**
     * Display contact information.
     */
    public function contact()
    {
        $contactInfo = [
            'email' => 'admin@shoppingrio.com',
            'phone' => '(0341) 555-1234',
            'address' => 'Av. Juan B. Justo 5000, Rosario, Santa Fe',
            'hours' => 'Lunes a Domingo: 10:00 - 22:00',
        ];

        return view('pages.static.contact', compact('contactInfo'));
    }

    /**
     * Display about page.
     */
    public function about()
    {
        $benefits = [
            'Inicial' => [
                'Acceso a promociones básicas',
                'Notificaciones por email',
                'Historial de compras',
            ],
            'Medium' => [
                'Acceso a promociones Medium e Inicial',
                'Notificaciones prioritarias',
                'Descuentos especiales',
            ],
            'Premium' => [
                'Acceso a todas las promociones',
                'Notificaciones exclusivas',
                'Eventos VIP',
                'Descuentos máximos',
            ],
        ];

        return view('pages.static.about', compact('benefits'));
    }
}
