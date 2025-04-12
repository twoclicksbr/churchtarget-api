<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonLeaderController extends Controller
{
    public function index(Request $request)
    {
        $idCredential = session('id_credential');
        $idTipoLider = 2; // ← ajuste aqui se o ID do tipo "líder" for diferente

        $leaders = DB::table('person')
            ->join('person_restriction', 'person.id', '=', 'person_restriction.id_person')
            ->where('person_restriction.id_type_user', $idTipoLider)
            ->where(function ($query) use ($idCredential) {
                $query->where('person.id_credential', $idCredential)
                      ->orWhere('person.id_credential', 1); // inclui registros públicos
            })
            ->select('person.id', 'person.name')
            ->orderBy('person.name')
            ->get();

        return response()->json(['leaders' => $leaders]);
    }
}
