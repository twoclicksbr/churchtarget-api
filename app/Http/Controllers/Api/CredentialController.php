<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Credential;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Helpers\LogHelper;


use Illuminate\Http\Request;

class CredentialController extends Controller
{
    public function index(Request $request)
    {
        $idCredential = session('id_credential');

        // Define base da query de acordo com a credencial
        if ($idCredential == 1) {
            $query = Credential::query(); // matriz vê tudo
        } else {
            $query = Credential::where('id', $idCredential); // filial vê só ela
        }

        // Filtro por id (IN)
        if ($request->filled('id') && is_array($request->id)) {
            $query->whereIn('id', $request->id);
        }

        // Filtros extras
        if ($request->filled('username')) {
            $query->where('username', 'like', '%' . $request->username . '%');
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active);
        } else {
            $query->where('active', 1); // garante active=1 se não vier no filtro
        }

        if ($request->filled('created_at_start')) {
            $query->whereDate('created_at', '>=', $request->created_at_start);
        }

        if ($request->filled('created_at_end')) {
            $query->whereDate('created_at', '<=', $request->created_at_end);
        }

        if ($request->filled('updated_at_start')) {
            $query->whereDate('updated_at', '>=', $request->updated_at_start);
        }

        if ($request->filled('updated_at_end')) {
            $query->whereDate('updated_at', '<=', $request->updated_at_end);
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');

        if (in_array($sortBy, ['id', 'username', 'active', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Paginação
        $perPage = $request->get('per_page', 10);
        $dados = $query->paginate($perPage)->through(function ($item) {
            return [
                'id' => $item->id,
                'username' => $item->username,
                'active' => $item->active,
                'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $item->updated_at->format('Y-m-d H:i:s'),
            ];
        });

        LogHelper::createLog('viewed', 'credential', 0);

        return response()->json([
            'credentials' => $dados,
            'applied_filters' => $request->all(),
            'options' => [
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder,
                'per_page' => $perPage,
            ],
        ]);
    }



    public function show($id)
    {
        $idCredential = session('id_credential');

        // Matriz pode ver qualquer, filial só o próprio
        if ($idCredential == 1) {
            $credential = Credential::where('id', $id)->where('active', 1)->first();
        } else {
            $credential = Credential::where('id', $id)
                ->where('id', $idCredential)
                ->where('active', 1)
                ->first();
        }

        if (!$credential) {
            return response()->json(['error' => 'Credencial não encontrada.'], 404);
        }

        LogHelper::createLog('show', 'credential', $credential->id);

        return response()->json([
            'id' => $credential->id,
            'username' => $credential->username,
            'active' => $credential->active,
            'created_at' => $credential->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $credential->updated_at->format('Y-m-d H:i:s'),
        ]);
    }



    public function store(Request $request)
    {
        $idCredential = session('id_credential');

        if ($idCredential != 1) {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }

        // Limpa o username: minúsculo e sem caracteres especiais
        $username = Str::lower(preg_replace('/[^a-z0-9]/i', '', $request->username));

        // Validação
        $validator = Validator::make([
            'username' => $username,
            'active' => $request->active,
        ], [
            'username' => [
                'required',
                'unique:credential,username',
                'regex:/^[a-z0-9]+$/'
            ],
            'active' => 'nullable|in:0,1',
        ], [
            'username.required' => 'O campo username é obrigatório.',
            'username.unique' => 'Este username já está em uso.',
            'username.regex' => 'O username deve conter apenas letras minúsculas e números.',
            'active.in' => 'O campo active deve ser 0 ou 1.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator, response()->json([
                'errors' => $validator->errors()
            ], 422));
        }

        // Define active = 1 se não enviado
        $active = $request->filled('active') ? $request->active : 1;

        $credential = Credential::create([
            'username' => $username,
            'token' => Str::random(40),
            'active' => $active,
        ]);

        LogHelper::createLog('created', 'credential', $credential->id, null, $credential->toArray());

        return response()->json([
            'id' => $credential->id,
            'username' => $credential->username,
            'token' => $credential->token,
            'active' => $credential->active,
            'created_at' => $credential->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $credential->updated_at->format('Y-m-d H:i:s'),
        ], 201);
    }





    public function update(Request $request, $id)
    {
        $idCredential = session('id_credential');

        // dd($idCredential);

        if ($idCredential != 1) {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }

        $credential = Credential::find($id);
        if (!$credential) {
            return response()->json(['error' => 'Credencial não encontrada.'], 404);
        }

        $username = Str::lower(preg_replace('/[^a-z0-9]/i', '', $request->username));

        $validator = Validator::make([
            'username' => $username,
            'active' => $request->active,
        ], [
            'username' => [
                'required',
                'unique:credential,username,' . $id,
                'regex:/^[a-z0-9]+$/'
            ],
            'active' => 'required|in:0,1',
        ], [
            'username.required' => 'O campo username é obrigatório.',
            'username.unique' => 'Este username já está em uso.',
            'username.regex' => 'O username deve conter apenas letras minúsculas e números, sem espaços ou símbolos.',
            'active.required' => 'O campo active é obrigatório.',
            'active.in' => 'O campo active deve ser 0 ou 1.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $old = $credential->toArray();

        $credential->update([
            'username' => $username,
            'active' => $request->active,
            'token' => Str::random(40),
        ]);

        LogHelper::createLog('updated', 'credential', $credential->id, $old, $credential->toArray());

        return response()->json([
            'id' => $credential->id,
            'username' => $credential->username,
            'token' => $credential->token,
            'active' => $credential->active,
            'created_at' => $credential->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $credential->updated_at->format('Y-m-d H:i:s'),
        ]);
    }



    public function destroy($id)
    {
        $idCredential = session('id_credential');

        if ($idCredential != 1) {
            return response()->json(['error' => 'Acesso não autorizado.'], 403);
        }

        $credential = Credential::find($id);
        if (!$credential) {
            return response()->json(['error' => 'Credencial não encontrada.'], 404);
        }

        $old = $credential->toArray();

        $credential->delete();

        LogHelper::createLog('deleted', 'credential', $credential->id, $old, null);

        return response()->json([
            'message' => 'Credencial excluída com sucesso.'
        ]);
    }



}
