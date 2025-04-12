<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\TypeUser;
use App\Models\{PersonUser, RecPassword};

use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Busca o usuário da credential atual
        $user = PersonUser::where('email', $request->email)
            ->where('id_credential', session('id_credential')) // ✅ garante que é da credential correta
            ->where('active', 1)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'E-mail ou senha inválidos.'], 401);
        }

        // Consulta as restrições da pessoa
        $typeUserIds = DB::table('person_restriction')
            ->where('id_person', $user->id_person)
            ->pluck('id_type_user');

        // Busca os nomes dos tipos de usuário
        $typeUserNames = TypeUser::whereIn('id', $typeUserIds)->pluck('name');

        // Monta o array com as permissões dinâmicas
        $authPermissions = [];
        foreach ($typeUserNames as $name) {
            $chave = 'auth_' . Str::slug($name, '_');
            $authPermissions[$chave] = true;
        }

        return response()->json(array_merge([
            'auth_id_person' => $user->id_person,
            'email' => $user->email,
        ], $authPermissions));
    }

    public function recPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = PersonUser::where('email', $request->email)
            ->where('active', 1)
            ->where('id_credential', session('id_credential'))
            ->first();

        if (! $user) {
            return response()->json(['error' => 'E-mail não encontrado.'], 404);
        }

        $token = (string) random_int(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(30);

        RecPassword::create([
            'id_credential' => session('id_credential'),
            'id_person_user' => $user->id,
            'email' => $user->email,
            'token' => $token,
            'expires_at' => $expiresAt,
        ]);

        // Dados do e-mail
        $data = [
            'userName' => $user->email,
            'verificationCode' => $token,
            'bannerUrl' => 'https://churchtarget.com/assets/default-banner.jpg',
            'events' => 'Ciclo do 30 Semanas 2025', // Pode vir da requisição futuramente
            'clienteNome' => config('app.name'),
        ];

        Mail::send('emails.checkEmail', $data, function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Recuperação de Senha');
        });

        return response()->json([
            'message' => 'Token de recuperação gerado e enviado por e-mail.',
            'token' => $token,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
        ]);
    }


    public function verifyRecoveryCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6',
        ]);

        $user = PersonUser::where('email', $request->email)
            ->where('active', 1)
            ->where('id_credential', session('id_credential'))
            ->first();

        if (! $user) {
            return response()->json(['error' => 'E-mail não encontrado.'], 404);
        }

        $recovery = RecPassword::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (! $recovery) {
            return response()->json(['error' => 'Token inválido.'], 400);
        }

        return response()->json(['message' => 'Token válido.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string|size:6',
            'password' => 'required|min:6',
        ]);

        $recovery = RecPassword::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (! $recovery) {
            return response()->json(['error' => 'Token inválido.'], 400);
        }

        $user = PersonUser::where('email', $request->email)
            ->where('active', 1)
            ->first();

        if (! $user) {
            return response()->json(['error' => 'Usuário não encontrado.'], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Apaga o token após uso
        $recovery->delete();

        return response()->json(['message' => 'Senha redefinida com sucesso.']);
    }
}
