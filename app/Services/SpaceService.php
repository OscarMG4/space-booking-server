<?php

namespace App\Services;

use App\Models\Space;

class SpaceService
{
    public function getSpaces(array $filters = [], int $perPage = 15)
    {
        $query = Space::query();

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_available'])) {
            $query->where('is_available', filter_var($filters['is_available'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['min_capacity'])) {
            $query->where('capacity', '>=', $filters['min_capacity']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price_per_hour', '<=', $filters['max_price']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage);
    }

    public function createSpace(array $data): Space
    {
        return Space::create($data);
    }

    public function updateSpace(Space $space, array $data): Space
    {
        $space->update($data);
        return $space;
    }

    public function canDeleteSpace(Space $space): bool
    {
        $activeBookings = $space->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('end_time', '>', now())
            ->count();

        return $activeBookings === 0;
    }

    public function deleteSpace(Space $space): void
    {
        if (!$this->canDeleteSpace($space)) {
            throw new \Exception('No se puede eliminar el espacio porque tiene reservas activas');
        }

        $space->delete();
    }

    public function getSpaceWithRelations(int $id): ?Space
    {
        return Space::with(['reviews', 'availabilities'])->find($id);
    }

    public function isSpaceAvailable(Space $space): bool
    {
        return $space->is_available;
    }

    public function validateCapacity(Space $space, int $attendeesCount): bool
    {
        return $attendeesCount <= $space->capacity;
    }
}
