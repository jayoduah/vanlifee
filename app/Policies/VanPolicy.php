<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Van;
use Illuminate\Auth\Access\Response;

class VanPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Van $van): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->isOwner()
            ? Response::allow()
            : Response::deny('Only van owners are authorized to create vans.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Van $van): Response
    {
        if (! $user->isOwner()) {
            return Response::deny('Only van owners are authorized to edit vans.');
        }

        return $user->id === $van->user_id
            ? Response::allow()
            : Response::deny('You do not own this van and cannot modify it.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Van $van): Response
    {
        if (! $user->isOwner()) {
            return Response::deny('Only van owners are authorized to delete vans.');
        }

        return $user->id === $van->user_id
            ? Response::allow()
            : Response::deny('You do not own this van and cannot delete it.');
    }
}
