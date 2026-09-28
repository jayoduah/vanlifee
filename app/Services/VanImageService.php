<?php

namespace App\Services;

use App\Models\Van;
use App\Models\VanImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VanImageService
{
    /**
     * Upload an image to Cloudinary and attach to Van.
     */
    public function upload(Van $van, UploadedFile $file, bool $isPrimary = false): VanImage
    {
        if ($isPrimary) {
            $van->images()->update(['is_primary' => false]);
        }

        // Upload to Cloudinary using the package helper.
        // If in testing environment, fallback to standard local storage since the macro isn't on Testing\File
        if (app()->environment('testing')) {
            $path = $file->store('vans', 'public');
        } else {
            $result = $file->storeOnCloudinary('vans');
            $path = $result->getSecurePath();
        }
        
        return $van->images()->create([
            'image_path' => $path,
            'is_primary' => $isPrimary,
        ]);
    }

    /**
     * Update an existing image (Replace file or update primary status)
     */
    public function update(VanImage $image, ?UploadedFile $file, ?bool $isPrimary): VanImage
    {
        if ($file) {
            if (app()->environment('testing')) {
                $path = $file->store('vans', 'public');
            } else {
                $result = $file->storeOnCloudinary('vans');
                $path = $result->getSecurePath();
            }
            $image->image_path = $path;
        }

        if ($isPrimary !== null) {
            if ($isPrimary) {
                VanImage::where('van_id', $image->van_id)->update(['is_primary' => false]);
            }
            $image->is_primary = $isPrimary;
        }

        $image->save();
        return $image;
    }

    /**
     * Delete an image
     */
    public function delete(VanImage $image): void
    {
        // For Cloudinary, you would ideally extract the public ID and delete it using Cloudinary::destroy($publicId)
        // Here we just delete the record for now as a basic implementation.
        $image->delete();
    }
}
