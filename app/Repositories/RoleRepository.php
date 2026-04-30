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
}
