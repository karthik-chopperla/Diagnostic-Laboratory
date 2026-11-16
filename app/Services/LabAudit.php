<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class LabAudit
{
    public static function log(string $action, string $module, ?string $recordId = null, ?string $details = null, ?string $user = null): void
    {
        AuditLog::create([
            'user_name' => $user ?? (Auth::user()?->name ?? 'System'),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'details' => $details,
            'ip_address' => request()->ip() ?? '127.0.0.1',
        ]);
    }
}
