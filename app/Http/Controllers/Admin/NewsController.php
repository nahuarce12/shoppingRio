<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNewsRequest;
use App\Models\News;
use App\Services\NewsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Admin controller for managing news/announcements.
 * Handles CRUD operations for shopping center news.
 */
class NewsController extends Controller
{
    public function __construct(
        private NewsService $newsService
    ) {
    }

    /**
     * Display a listing of news.
     */
    public function index(Request $request)
    {
        return redirect()->route('admin.dashboard', ['section' => 'novedades']);
    }

    /**
     * Show the form for creating a new news item.
     */
    public function create()
    {
        $categories = ['Inicial', 'Medium', 'Premium'];
        $defaultDuration = config('shopping.news.default_duration_days', 30);

        return view('admin.news.create', compact('categories', 'defaultDuration'));
    }

    /**
     * Store a newly created news item in storage.
     */
    public function store(StoreNewsRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Handle image upload
            if ($request->hasFile('imagen')) {
                $imagePath = $request->file('imagen')->store('news/images', 'public');
                $data['imagen'] = $imagePath;
            }
            
            $news = News::create($data);

            return redirect()
                ->route('admin.dashboard', ['section' => 'novedades'])
                ->with('success', 'Novedad creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al crear la novedad. Por favor intente nuevamente.');
        }
    }

    /**
     * Display the specified news item.
     */
    public function show(News $news)
    {
        // Calculate who can see this news based on category hierarchy
        $visibleTo = match($news->categoria_destino) {
            'Inicial' => ['Inicial', 'Medium', 'Premium'],
            'Medium' => ['Medium', 'Premium'],
            'Premium' => ['Premium'],
            default => []
        };

        // Get client count per category
        $clientCounts = [
            'Inicial' => \App\Models\User::where('tipo_usuario', 'cliente')
                ->where('categoria_cliente', 'Inicial')
                ->count(),
            'Medium' => \App\Models\User::where('tipo_usuario', 'cliente')
                ->where('categoria_cliente', 'Medium')
                ->count(),
            'Premium' => \App\Models\User::where('tipo_usuario', 'cliente')
                ->where('categoria_cliente', 'Premium')
                ->count(),
        ];

        $totalVisible = array_sum(array_map(fn($cat) => $clientCounts[$cat] ?? 0, $visibleTo));

        return view('admin.news.show', compact('news', 'visibleTo', 'clientCounts', 'totalVisible'));
    }

    /**
     * Show the form for editing the specified news item.
     */
    public function edit(News $news)
    {
        return redirect()->route('admin.dashboard', ['section' => 'novedades']);
    }

    /**
     * Update the specified news item in storage.
     */
    public function update(StoreNewsRequest $request, News $news)
    {
        try {
            $data = $request->validated();
            
            // Handle image upload
            if ($request->hasFile('imagen')) {
                // Delete old image if exists
                if ($news->imagen && Storage::disk('public')->exists($news->imagen)) {
                    Storage::disk('public')->delete($news->imagen);
                }
                
                $imagePath = $request->file('imagen')->store('news/images', 'public');
                $data['imagen'] = $imagePath;
            }
            
            $news->update($data);

            return redirect()
                ->route('admin.dashboard', ['section' => 'novedades'])
                ->with('success', 'Novedad actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Error al actualizar la novedad. Por favor intente nuevamente.');
        }
    }

    /**
     * Remove the specified news item from storage.
     */
    public function destroy(News $news)
    {
        try {
            // Delete image if exists
            if ($news->imagen && Storage::disk('public')->exists($news->imagen)) {
                Storage::disk('public')->delete($news->imagen);
            }
            
            $news->delete();

            return redirect()
                ->route('admin.dashboard', ['section' => 'novedades'])
                ->with('success', 'Novedad eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error al eliminar la novedad. Por favor intente nuevamente.');
        }
    }

    /**
     * Display expired news for review/deletion.
     */
    public function expired()
    {
        $expiredNews = News::where('fecha_hasta', '<', now())
            ->orderBy('fecha_hasta', 'desc')
            ->paginate(20);

        $retentionDays = config('shopping.scheduled_jobs.news_cleanup.retention_days', 30);

        return view('admin.news.expired', compact('expiredNews', 'retentionDays'));
    }
}
