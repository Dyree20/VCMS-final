<?php

namespace App\Http\Controllers;

use App\Models\Clamping;
use App\Models\AdvancedSearch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(): View
    {
        $savedSearches = auth()->user()->advancedSearches;
        return view('admin.search.index', compact('savedSearches'));
    }

    public function search(Request $request)
    {
        $filters = $request->validate([
            'plate_no' => 'nullable|string',
            'enforcer_id' => 'nullable|integer',
            'status' => 'nullable|string',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'zone_id' => 'nullable|integer',
            'min_fine' => 'nullable|numeric',
            'max_fine' => 'nullable|numeric',
        ]);

        $query = Clamping::query();

        if ($filters['plate_no'] ?? null) {
            $query->where('plate_no', 'like', '%' . $filters['plate_no'] . '%');
        }

        if ($filters['enforcer_id'] ?? null) {
            $query->where('user_id', $filters['enforcer_id']);
        }

        if ($filters['status'] ?? null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['date_from'] ?? null) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to'] ?? null) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if ($filters['zone_id'] ?? null) {
            $query->where('parking_zone_id', $filters['zone_id']);
        }

        if ($filters['min_fine'] ?? null) {
            $query->where('fine_amount', '>=', $filters['min_fine']);
        }

        if ($filters['max_fine'] ?? null) {
            $query->where('fine_amount', '<=', $filters['max_fine']);
        }

        $results = $query->with(['user', 'zone'])->paginate(20);

        return view('admin.search.results', compact('results', 'filters'));
    }

    public function saveSearch(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'filters' => 'required|array',
            'is_public' => 'boolean',
        ]);

        auth()->user()->advancedSearches()->create($validated);

        return response()->json(['success' => true, 'message' => 'Search saved successfully']);
    }

    public function loadSearch(AdvancedSearch $search)
    {
        if ($search->user_id !== auth()->id() && !$search->is_public) {
            abort(403);
        }

        return response()->json($search->filters);
    }
}
