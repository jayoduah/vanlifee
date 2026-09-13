<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Van;
use App\Models\VanImage;
use Illuminate\Auth\Access\Response;

class VanImagePolicy
{
    /**
     * Determine whether the user can view any images.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view an image.
     */
    public function view(?User $user, VanImage $vanImage): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create/upload an image for a specific van.
     */
    public function create(User $user, Van $van): Response
    {
        if (! $user->isOwner()) {
            return Response::deny('Only van owners are authorized to manage van images.');
        }

        return $user->id === $van->user_id
            ? Response::allow()
            : Response::deny('You cannot upload images to a van you do not own.');
    }

    /**
     * Determine whether the user can update/replace a van image.
     */
    public function update(User $user, VanImage $vanImage): Response
    {
        if (! $user->isOwner()) {
            return Response::deny('Only van owners are authorized to manage van images.');
        }

        return $user->id === $vanImage->van->user_id
            ? Response::allow()
            : Response::deny('You cannot replace images on a van you do not own.');
    }

    /**
     * Determine whether the user can delete a van image.
     */
    public function delete(User $user, VanImage $vanImage): Response
    {
        if (! $user->isOwner()) {
            return Response::deny('Only van owners are authorized to manage van images.');
        }

        return $user->id === $vanImage->van->user_id
            ? Response::allow()
            : Response::deny('You cannot delete images on a van you do not own.');
    }
}
