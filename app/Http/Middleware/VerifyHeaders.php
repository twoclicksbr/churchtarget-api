<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Credential;
use Illuminate\Support\Facades\Log;

class VerifyHeaders
{
    public function handle(Request $request, Closure $next)
    {

        // Log::info("Passei por aqui");
        
        $username = $request->header('username');
        $token = $request->header('token');

        if (!$username) {
            return response()->json(['error' => 'Informe o username no header.'], 401);
        }

        if (!$token) {
            return response()->json(['error' => 'Informe o token no header.'], 401);
        }

        // Verifica credencial no banco
        $credential = Credential::where('username', $username)
            ->where('token', $token)
            ->where('active', 1)
            ->first();

        if (!$credential) {
            return response()->json(['error' => 'Credenciais inválidas.'], 401);
        }

        // Salvar id_credential na sessão
        session(['id_credential' => $credential->id]);

        // Valida idPerson para rotas admin
        if ($request->is('api/v1/admin/*')) {
            $idPerson = $request->header('idPerson') ?? session('auth_id_person');

            if (!$idPerson) {
                return response()->json(['error' => 'O cabeçalho idPerson é obrigatório para rotas administrativas.'], 401);
            }

            session(['auth_id_person' => $idPerson]);
        }

        return $next($request);
    }

}

