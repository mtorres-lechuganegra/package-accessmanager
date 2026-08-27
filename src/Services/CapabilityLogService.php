<?php

namespace LechugaNegra\AccessManager\Services;

use Illuminate\Support\Facades\Log;
use LechugaNegra\AccessManager\Models\CapabilityLog;

class CapabilityLogService
{
    /**
     * Registrar un log de auditoría.
     *
     * @param array $data Datos del log a registrar.
     * @return void
     */
    public static function register(array $data): void
    {
        try {
            CapabilityLog::create($data);
        } catch (\Exception $e) {
            Log::error("CapabilityLogService.register: {$e->getMessage()}", $data);
        }
    }
}
