<?php

namespace App\Repositories;

use App\Models\Item;
use App\Repositories\Contracts\ItemRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ItemRepository implements ItemRepositoryInterface
{
    public function __construct(protected Item $model)
    {
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function getSummary(): array
    {
        return [
            'total_quantity' => $this->model->sum('quantity'),
            'total_value' => $this->model->sum(DB::raw('price * quantity')),
        ];
    }

    public function create(array $data): Item
    {
        return $this->model->create($data);
    }

    public function find(int $id): ?Item
    {
        return $this->model->find($id);
    }

    public function update(array $data, int $id): ?Item
    {
        $item = $this->model->find($id);

        if (!$item) {
            return null;
        }

        $item->update($data);

        return $item->fresh();
    }

    public function delete(int $id): bool
    {
        $item = $this->model->find($id);

        if (!$item) {
            return false;
        }

        return $item->delete();
    }
}