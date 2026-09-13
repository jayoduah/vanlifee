<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVanRequest;
use App\Http\Requests\UpdateVanRequest;
use App\Models\Van;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VanController extends Controller
{
   
    public function index(Request $request): JsonResponse
    {
        $query = Van::with(['owner:id,name,email,phone_number', 'images']);

        // to filtering by make, model, or year
        if ($request->filled('make')) {
            $query->where('make', 'like', '%' . $request->make . '%');
        }
        if ($request->filled('model')) {
            $query->where('model', 'like', '%' . $request->model . '%');
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        $vans = $query->latest()->paginate(15);

        return response()->json($vans, 200);
    }

    public function show(Van $van): JsonResponse
    {
        $van->load([
            'owner:id,name,email,phone_number',
            'images',
            'reviews.customer:id,name,email',
        ]);

        return response()->json([
            'van' => $van,
        ], 200);
    }

    public function myVans(Request $request): JsonResponse
    {
        $vans = Van::where('user_id', $request->user()->id)
            ->with(['images', 'reviews'])
            ->latest()
            ->paginate(15);

        return response()->json($vans, 200);
    }

    public function store(StoreVanRequest $request): JsonResponse
    {
        Gate::authorize('create', Van::class);

        $van = $request->user()->vans()->create($request->validated());

        return response()->json([
            'message' => 'Van created successfully.',
            'van' => $van->load('owner:id,name,email,phone_number'),
        ], 201);
    }

    
    public function update(UpdateVanRequest $request, Van $van): JsonResponse
    {
        Gate::authorize('update', $van);

        $van->update($request->validated());

        return response()->json([
            'message' => 'Van updated successfully.',
            'van' => $van->fresh(['owner:id,name,email,phone_number', 'images']),
        ], 200);
    }


    public function destroy(Van $van): JsonResponse
    {
        Gate::authorize('delete', $van);

        $van->delete();

        return response()->json([
            'message' => 'Van deleted successfully.',
        ], 200);
    }
}
