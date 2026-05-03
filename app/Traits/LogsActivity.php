<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log a custom activity.
     */
    protected function logActivity(string $action, ?string $model = null, ?int $modelId = null, array $details = [])
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => strtoupper($action),
            'model'      => $model,
            'model_id'   => $modelId,
            'details'    => $details,
            'ip_address' => Request::ip(),
            'user_agent' => Request::header('User-Agent'),
        ]);
    }
}
