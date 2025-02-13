<?php

namespace App\Services\Impls;

use Exception;
use App\Services\SystemService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SystemServiceImpl implements SystemService
{

    public function checkDBConnection()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function isExistTable($tableName = null)
    {
        if ($tableName == null) {
            return Schema::hasTable('users');
        } else {
            return Schema::hasTable($tableName);
        }
    }
}
