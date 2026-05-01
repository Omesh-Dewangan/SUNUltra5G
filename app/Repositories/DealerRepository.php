<?php

namespace App\Repositories;

use App\Models\Dealer;
use Illuminate\Support\Facades\DB;

class DealerRepository
{
    public function getAll($filters = [])
    {
        $query = Dealer::with('creator')->latest();

        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('gstin', 'like', "%$s%");
            });
        }

        if (!empty($filters['type'])) {
            $query->where('dealer_type', $filters['type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->paginate(15);
    }

    public function findById($id)
    {
        return Dealer::findOrFail($id);
    }

    public function store(array $data)
    {
        return Dealer::create($data);
    }

    public function update($id, array $data)
    {
        $dealer = $this->findById($id);
        $dealer->update($data);
        return $dealer;
    }

    public function delete($id)
    {
        $dealer = $this->findById($id);
        return $dealer->delete();
    }
}
