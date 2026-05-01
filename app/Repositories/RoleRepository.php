<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class RoleRepository
{
    protected $model;

    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    public function getAll(): Collection
    {
        return $this->model->with('permissions')->get();
    }

    public function findById(int $id): ?Role
    {
        return $this->model->with('permissions')->find($id);
    }

    public function findBySlug(string $slug): ?Role
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function create(array $data): Role
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $role = $this->findById($id);
        if (!$role) return false;
        return $role->update($data);
    }

    public function delete(int $id): bool
    {
        $role = $this->findById($id);
        if (!$role) return false;
        return $role->delete();
    }
}
