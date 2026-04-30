<?php

namespace App\Repositories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class PermissionRepository
{
    protected $model;

    public function __construct(Permission $permission)
    {
        $this->model = $permission;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }
}
