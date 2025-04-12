{{-- resources/views/emails/passwordChanged.blade.php --}}
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senha Alterada</title>
</head>

<body style="font-family: Arial, sans-serif; text-align: center; background-color: #f8f9fa; padding: 20px;">

    <div style="background: #ffffff; padding: 20px; border-radius: 10px; max-width: 600px; margin: 0 auto;">

        <!-- Banner personalizado -->
        <img src="{{ $bannerUrl ?? 'https://churchtarget.com/assets/default-banner.jpg' }}" alt="{{ config('app.name') }}"
            style="width: 100%; max-width: 600px; margin-bottom: 20px;">

        <h2>Olá, {{ $userName }}!</h2>
        <p>Sua senha foi alterada com sucesso para o <strong>{{ $events }}</strong>.</p>

        <p>Se você realizou essa alteração, nenhuma ação adicional é necessária.</p>
        <p>Se não foi você, entre em contato imediatamente com o suporte.</p>

        <p style="margin-top: 20px;">Atenciosamente,<br><strong>{{ $clienteNome }}</strong></p>
    </div>

    <footer style="margin-top: 30px; font-size: 12px; color: #6c757d;">
        &copy; {{ date('Y') }} - Power by: <a href="https://churchtarget.com"
            target="_blank">{{ config('app.name') }}</a>. <br> Todos os direitos reservados.
    </footer>

</body>

</html>
