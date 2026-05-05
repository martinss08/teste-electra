<?php

namespace App\Service;

use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ItemService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Item $model)
    {    }

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
        return $this->model->findOrFail($id);
    }

    public function update(array $data, int $id): ?Item
    {
        $item = $this->find($id);

        if (!$item) {
            return null;
        }

        $item->update($data);

        return $item;
    }

    public function delete(int $id): ?Item
    {
        $item = $this->find($id);

        if (!$item) {
            return null;
        }

        $item->delete();

        return $item;
    }
}
