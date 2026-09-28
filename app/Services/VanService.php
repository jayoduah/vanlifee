<?php

namespace App\Services;

use App\Models\Van;
use Illuminate\Pagination\LengthAwarePaginator;

class VanService
{
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Van::with(['owner:id,name,email,phone_number', 'images']);

        if (!empty($filters['make'])) {
            $query->where('make', 'like', '%' . $filters['make'] . '%');
        }
        if (!empty($filters['model'])) {
            $query->where('model', 'like', '%' . $filters['model'] . '%');
        }
        if (!empty($filters['year'])) {
            $query->where('year', $filters['year']);
        }
        if (!empty($filters['tag'])) {
            $query->where('tag', $filters['tag']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getDetails(Van $van): Van
    {
        $van->load([
            'owner:id,name,email,phone_number',
            'images',
            'reviews.customer:id,name,email',
        ]);
        
        return $van;
    }

    public function getMyVans(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Van::where('user_id', $userId)
            ->with(['images', 'reviews'])
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data, int $userId): Van
    {
        return Van::create(array_merge($data, ['user_id' => $userId]));
    }

    public function update(Van $van, array $data): Van
    {
        $van->update($data);
        return $van->fresh(['owner:id,name,email,phone_number', 'images']);
    }

    public function delete(Van $van): void
    {
        $van->delete();
    }
}
