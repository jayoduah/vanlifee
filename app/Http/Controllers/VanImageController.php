<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVanImageRequest;
use App\Http\Requests\UpdateVanImageRequest;
use App\Models\Van;
use App\Models\VanImage;
use App\Services\VanImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class VanImageController extends Controller
{
    public function __construct(private VanImageService $vanImageService)
    {
    }

    public function index(Van $van): JsonResponse
    {
        return response()->json([
            'van_id' => $van->id,
            'images' => $van->images()->latest()->get(),
        ], 200);
    }

    public function store(StoreVanImageRequest $request, Van $van): JsonResponse
    {
        Gate::authorize('create', [VanImage::class, $van]);

        $image = $this->vanImageService->upload(
            $van, 
            $request->file('image'), 
            $request->boolean('is_primary', false)
        );

        return response()->json([
            'message' => 'Image uploaded successfully.',
            'image' => $image,
        ], 201);
    }

    public function update(UpdateVanImageRequest $request, VanImage $image): JsonResponse
    {
        Gate::authorize('update', $image);

        $image = $this->vanImageService->update(
            $image,
            $request->file('image'),
            $request->has('is_primary') ? $request->boolean('is_primary') : null
        );

        return response()->json([
            'message' => 'Image updated successfully.',
            'image' => $image,
        ], 200);
    }

    public function destroy(VanImage $image): JsonResponse
    {
        Gate::authorize('delete', $image);

        $this->vanImageService->delete($image);

        return response()->json([
            'message' => 'Image deleted successfully.',
        ], 200);
    }
}
