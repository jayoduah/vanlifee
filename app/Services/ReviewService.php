<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Van;
use Illuminate\Database\Eloquent\Collection;

class ReviewService
{
    public function getForVan(Van $van): Collection
    {
        return $van->reviews()->with('customer:id,name')->latest()->get();
    }

    public function create(Van $van, int $userId, array $data): Review
    {
        return $van->reviews()->create([
            'user_id' => $userId,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);
    }

    public function update(Review $review, array $data): Review
    {
        $review->update($data);
        return $review;
    }

    public function delete(Review $review): void
    {
        $review->delete();
    }
}
