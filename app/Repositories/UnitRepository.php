<?php

namespace App\Repositories;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;

class UnitRepository
{
    protected $model;

    public function __construct(Unit $unit)
    {
        $this->model = $unit;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Unit
    {
        return $this->model->find($id);
    }

    public function create(array $data): Unit
    {
        return $this->model->create($data);
    }
    public function update(int $id, array $data): bool
    {
        $unit = $this->findById($id);
        if ($unit) {
            return $unit->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $unit = $this->findById($id);
        if ($unit) {
            return $unit->delete();
        }
        return false;
    }
}
