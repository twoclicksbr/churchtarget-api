<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LogHelper
{
    public static function createLog($action, $table, $recordId, $oldData = null, $newData = null)
    {
        $idCredential = session('id_credential');
        $idPerson = session('auth_id_person') ?? request()->header('idPerson');

        if (!$idPerson || !$idCredential) {
            // Só grava log se tiver ambos
            return;
        }

        DB::table('log')->insert([
            'action' => $action,
            'table' => $table,
            'record_id' => $recordId,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'id_credential' => $idCredential,
            'id_person' => $idPerson,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
