<?php

namespace App\Service;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;

class ItemService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Item $model)
    {    }

    public function all(): Collection
    {
        return $this->model->all();
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
