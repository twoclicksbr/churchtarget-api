<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicEmail;
use App\Models\Api\EmailContent;

class EmailPreviewController extends Controller
{
    public function sendTest(Request $request)
    {
        // dd(session('id_credential'));

        $request->validate([
            'to' => 'required|email',
            'type_email' => 'required|integer',
        ]);

        $emailContent = EmailContent::where('id_type_email', $request->type_email)
            ->where('id_credential', session('id_credential'))
            ->first();

        if (! $emailContent) {
            return response()->json(['error' => 'Tipo de e-mail não encontrado.'], 404);
        }

        $html = Blade::render($emailContent->body, [
            'userName' => 'Alex de Exemplo',
            'verificationCode' => '123456',
            'bannerUrl' => $emailContent->banner_url,
        ]);

        Mail::to($request->to)->send(new DynamicEmail($html, $emailContent->subject));

        return response()->json(['message' => 'E-mail enviado com sucesso!']);
    }
}
