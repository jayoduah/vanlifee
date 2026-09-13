<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Models\Van;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any reviews.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create a review for a van.
     */
    public function create(User $user, Van $van): Response
    {
        if (! $user->isCustomer()) {
            return Response::deny('Only customers are allowed to submit reviews.');
        }

        if ($user->id === $van->user_id) {
            return Response::deny('Van owners cannot review their own vans.');
        }

        $existingReview = Review::where('van_id', $van->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($existingReview) {
            return Response::deny('You have already reviewed this van.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the review.
     */
    public function update(User $user, Review $review): Response
    {
        return $user->id === $review->user_id
            ? Response::allow()
            : Response::deny('You are not authorized to edit this review.');
    }

    /**
     * Determine whether the user can delete the review.
     */
    public function delete(User $user, Review $review): Response
    {
        return $user->id === $review->user_id
            ? Response::allow()
            : Response::deny('You are not authorized to delete this review.');
    }
}
