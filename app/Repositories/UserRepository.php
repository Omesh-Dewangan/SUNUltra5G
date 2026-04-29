<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function update(int $userId, array $data): ?User
    {
        $user = $this->model->find($userId);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }
}
