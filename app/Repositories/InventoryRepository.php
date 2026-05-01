<?php

namespace App\Repositories;

use App\Models\Inventory;
use Illuminate\Database\Eloquent\Collection;

class InventoryRepository
{
    protected $model;

    public function __construct(Inventory $inventory)
    {
        $this->model = $inventory;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function getAllWithCategory(): Collection
    {
        return $this->model->with('category')->get();
    }

    public function findById(int $id): ?Inventory
    {
        return $this->model->find($id);
    }

    public function findForUpdate(int $id): ?Inventory
    {
        return $this->model->lockForUpdate()->find($id);
    }

    public function findByCode(string $code): ?Inventory
    {
        return $this->model->where('code', $code)->first();
    }
    public function update(int $id, array $data): bool
    {
        $inventory = $this->findById($id);
        if ($inventory) {
            return $inventory->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $inventory = $this->findById($id);
        if ($inventory) {
            return $inventory->delete();
        }
        return false;
    }
}
