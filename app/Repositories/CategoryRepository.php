<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository
{
    protected $model;

    public function __construct(Category $category)
    {
        $this->model = $category;
    }

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function findById(int $id): ?Category
    {
        return $this->model->find($id);
    }

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }
    public function update(int $id, array $data): bool
    {
        $category = $this->findById($id);
        if ($category) {
            return $category->update($data);
        }
        return false;
    }

    public function delete(int $id): bool
    {
        $category = $this->findById($id);
        if ($category) {
            return $category->delete();
        }
        return false;
    }
}
