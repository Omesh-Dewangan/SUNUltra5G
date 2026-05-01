<?php

namespace App\Services;

use App\Repositories\DealerRepository;
use Exception;

class DealerService
{
    protected $dealerRepository;

    public function __construct(DealerRepository $dealerRepository)
    {
        $this->dealerRepository = $dealerRepository;
    }

    public function getDealers($filters = [])
    {
        return $this->dealerRepository->getAll($filters);
    }

    public function createDealer(array $data, $userId)
    {
        $data['created_by'] = $userId;
        return $this->dealerRepository->store($data);
    }

    public function updateDealer($id, array $data)
    {
        return $this->dealerRepository->update($id, $data);
    }

    public function deleteDealer($id)
    {
        return $this->dealerRepository->delete($id);
    }

    public function toggleStatus($id)
    {
        $dealer = $this->dealerRepository->findById($id);
        return $this->dealerRepository->update($id, ['is_active' => !$dealer->is_active]);
    }
}
