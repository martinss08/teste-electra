<?php

namespace App\Repositories\Contracts;

use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ItemRepositoryInterface
{
    public function paginate(int $perPage = 10): LengthAwarePaginator;

    public function getSummary(): array;

    public function create(array $data): Item;

    public function find(int $id): ?Item;

    public function update(array $data, int $id): ?Item;
}
