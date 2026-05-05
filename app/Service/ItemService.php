<?php

namespace App\Service;

use App\Models\Item;
use App\Repositories\Contracts\ItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ItemService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected ItemRepositoryInterface $repository)
    {
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function getSummary(): array
    {
        return $this->repository->getSummary();
    }

    public function create(array $data): Item
    {
        return $this->repository->create($data);
    }

    public function find(int $id): ?Item
    {
        return $this->repository->find($id);
    }

    public function update(array $data, int $id): ?Item
    {
        return $this->repository->update($data, $id);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
