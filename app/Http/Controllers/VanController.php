<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVanRequest;
use App\Http\Requests\UpdateVanRequest;
use App\Models\Van;
use App\Services\VanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VanController extends Controller
{
    public function __construct(private VanService $vanService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $vans = $this->vanService->getAll($request->all());
        return response()->json($vans, 200);
    }

    public function show(Van $van): JsonResponse
    {
        $van = $this->vanService->getDetails($van);

        return response()->json([
            'van' => $van,
        ], 200);
    }

    public function myVans(Request $request): JsonResponse
    {
        $vans = $this->vanService->getMyVans($request->user()->id);
        return response()->json($vans, 200);
    }

    public function store(StoreVanRequest $request): JsonResponse
    {
        Gate::authorize('create', Van::class);

        $van = $this->vanService->create($request->validated(), $request->user()->id);

        return response()->json([
            'message' => 'Van created successfully.',
            'van' => $van->load('owner:id,name,email,phone_number'),
        ], 201);
    }

    public function update(UpdateVanRequest $request, Van $van): JsonResponse
    {
        Gate::authorize('update', $van);

        $van = $this->vanService->update($van, $request->validated());

        return response()->json([
            'message' => 'Van updated successfully.',
            'van' => $van,
        ], 200);
    }

    public function destroy(Van $van): JsonResponse
    {
        Gate::authorize('delete', $van);

        $this->vanService->delete($van);

        return response()->json([
            'message' => 'Van deleted successfully.',
        ], 200);
    }
}
