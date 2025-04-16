<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\RecPasswordRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\VerifyRecoveryCodeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Api\TypeUser;
use App\Models\Api\{EmailConfig, PersonUser, RecPassword};

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

    protected function sendEmailTemplate($user, $defaultTemplate, $subject, $code = null)
    {
        $config = EmailConfig::with('type')
            ->where('id_credential', session('id_credential'))
            ->where('id_ministry', $user->id_ministry ?? 1)
            ->where('active', 1)
            ->first();

        
        $templateView = $config->type->template ?? $defaultTemplate;

        $data = [
            'userName' => $user->email,
            'verificationCode' => $code,
            'bannerUrl' => $config->banner_url ?? 'https://churchtarget.com/assets/default-banner.jpg',
            'events' => $config->events ?? 'Evento padrão',
            'clienteNome' => $config->client_name ?? config('app.name'),
        ];

        Mail::send("emails.$templateView", $data, function ($message) use ($user, $subject) {
            $message->to($user->email)->subject($subject);
        });
    }


    public function recPassword(RecPasswordRequest $request)
    {
        $user = PersonUser::select('id', 'email', 'id_person', 'id_credential')
            ->where('email', $request->email)
            ->where('active', 1)
            ->where('id_credential', session('id_credential'))
            ->first();


        if (! $user) {
            return response()->json(['error' => 'E-mail não encontrado.'], 404);
        }

        $token = (string) random_int(100000, 999999);

        RecPassword::where('email', $request->email)
            ->where('id_credential', session('id_credential'))
            ->delete();

        RecPassword::create([
            'id_credential' => session('id_credential'),
            'id_person' => $user->id_person, // <- esse aqui
            'email' => $user->email,
            'token' => $token,
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        // Envia e-mail com template
        $this->sendEmailTemplate($user, 'recPassword', 'Recuperação de Senha', $token);

        return response()->json([
            'message' => 'Token de recuperação gerado e enviado por e-mail.',
            'token' => $token,
        ]);
    }

    public function verifyRecoveryCode(VerifyRecoveryCodeRequest $request)
    {
        $user = PersonUser::where('email', $request->email)
            ->where('active', 1)
            ->where('id_credential', session('id_credential')) // <- isso aqui pode ser o problema
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

    public function resetPassword(ResetPasswordRequest $request)
    {
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

        $recovery->delete();

        // Envia e-mail de confirmação de alteração
        $this->sendEmailTemplate($user, 'passwordChanged', 'Senha Alterada com Sucesso');

        return response()->json(['message' => 'Senha redefinida com sucesso.']);
    }

}
