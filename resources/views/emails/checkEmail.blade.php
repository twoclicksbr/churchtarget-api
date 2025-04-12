<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-mail</title>
</head>
<body style="font-family: Arial, sans-serif; text-align: center; background-color: #f8f9fa; padding: 20px;">

    <div style="background: #ffffff; padding: 20px; border-radius: 10px; max-width: 600px; margin: 0 auto;">

        <!-- Banner personalizado -->
        <img src="{{ $bannerUrl ?? 'https://churchtarget.com/assets/default-banner.jpg' }}" alt="{{ config('app.name') }}" style="width: 100%; max-width: 600px; margin-bottom: 20px;">

        <h2>Olá, {{ $userName }}!</h2>
        <p>Parabéns por fazer a sua inscrição para o <strong>{{ $events }}</strong>.</p>

        @isset($verificationCode)
            <p>Mas antes de tudo, precisamos verificar seu e-mail. Utilize o código de verificação:</p>
            <h1 style="color: #2c3e50; font-size:30px; text-align-last: center">{{ $verificationCode }}</h1>
            <p>Insira este código na página de verificação para concluir seu registro.</p>
        @endisset

        <p>Se você não solicitou este cadastro, ignore este e-mail.</p>

        <p style="margin-top: 20px;">Atenciosamente,<br><strong>{{ $clienteNome }}</strong></p>
    </div>

    <footer style="margin-top: 30px; font-size: 12px; color: #6c757d;">
        &copy; {{ date('Y') }} - Power by: <a href="https://churchtarget.com" target="_blank">{{ config('app.name') }}</a>. <br> Todos os direitos reservados.
    </footer>

</body>
</html>
