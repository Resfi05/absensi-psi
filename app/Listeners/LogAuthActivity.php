<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthActivity
{
    public function handleLogin(Login $event): void
    {
        ActivityLog::create([
            'user_id'    => $event->user->id,
            'aksi'       => 'login',
            'model_type' => null,
            'model_id'   => null,
            'deskripsi'  => "{$event->user->name} login ke sistem",
            'ip_address' => request()->ip(),
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        if (!$event->user) return;

        ActivityLog::create([
            'user_id'    => $event->user->id,
            'aksi'       => 'logout',
            'model_type' => null,
            'model_id'   => null,
            'deskripsi'  => "{$event->user->name} logout dari sistem",
            'ip_address' => request()->ip(),
        ]);
    }
}