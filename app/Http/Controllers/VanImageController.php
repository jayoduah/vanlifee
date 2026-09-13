<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVanImageRequest;
use App\Http\Requests\UpdateVanImageRequest;
use App\Models\Van;
use App\Models\VanImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class VanImageController extends Controller
{
    /**
     * Display a listing of images belonging to the specified van.
     */
    public function index(Van $van): JsonResponse
    {
        return response()->json([
            'van_id' => $van->id,
            'images' => $van->images()->latest()->get(),
        ], 200);
    }

    /**
     * Upload and attach a new image to the van (Owner only).
     */
    public function store(StoreVanImageRequest $request, Van $van): JsonResponse
    {
        Gate::authorize('create', [VanImage::class, $van]);

        $isPrimary = $request->boolean('is_primary', false);

        // If this new image is marked as primary, reset any previous primary images for this van
        if ($isPrimary) {
            $van->images()->update(['is_primary' => false]);
        }

        $path = $request->file('image')->store('vans', 'public');

        $image = $van->images()->create([
            'image_path' => $path,
            'is_primary' => $isPrimary,
        ]);

        return response()->json([
            'message' => 'Image uploaded successfully.',
            'image' => $image,
        ], 201);
    }

    /**
     * Replace an existing image file or update primary status (Owner only).
     */
    public function update(UpdateVanImageRequest $request, VanImage $image): JsonResponse
    {
        Gate::authorize('update', $image);

        if ($request->hasFile('image')) {
            // Delete the old file from storage
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }

            $newPath = $request->file('image')->store('vans', 'public');
            $image->image_path = $newPath;
        }

        if ($request->has('is_primary')) {
            $isPrimary = $request->boolean('is_primary');
            if ($isPrimary) {
                VanImage::where('van_id', $image->van_id)->update(['is_primary' => false]);
            }
            $image->is_primary = $isPrimary;
        }

        $image->save();

        return response()->json([
            'message' => 'Image updated successfully.',
            'image' => $image,
        ], 200);
    }

    /**
     * Delete an image from storage and database (Owner only).
     */
    public function destroy(VanImage $image): JsonResponse
    {
        Gate::authorize('delete', $image);

        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return response()->json([
            'message' => 'Image deleted successfully.',
        ], 200);
    }
}
