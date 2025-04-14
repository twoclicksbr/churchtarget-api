<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('dev-emails')->group(function () {
    Route::get('/checkEmail', function () {
        $userName = 'Fulano da Silva';
        $verificationCode = '123456';
        $bannerUrl = 'https://30semanas.com.br/assets/img/banner.jpg';
        $events = 'Ciclo do 30 Semanas 2025';
        $clienteNome = 'Equipe 30 Semanas';

        return view('emails.checkEmail', compact('userName', 'verificationCode', 'bannerUrl', 'events', 'clienteNome'));
    });

    Route::get('/welcome', function () {
        $userName = 'Fulano da Silva';
        $verificationCode = '123456';
        $bannerUrl = 'https://30semanas.com.br/assets/img/banner.jpg';
        $events = 'Ciclo do 30 Semanas 2025';
        $clienteNome = 'Equipe 30 Semanas';

        return view('emails.welcome', compact('userName', 'verificationCode', 'bannerUrl', 'events', 'clienteNome'));
    });

    Route::get('/recPassword', function () {
        $userName = 'Fulano da Silva';
        $verificationCode = '123456';
        $bannerUrl = 'https://30semanas.com.br/assets/img/banner.jpg';
        $events = 'Ciclo do 30 Semanas 2025';
        $clienteNome = 'Equipe 30 Semanas';

        return view('emails.recPassword', compact('userName', 'verificationCode', 'bannerUrl', 'events', 'clienteNome'));
    });

    Route::get('/passwordChanged', function () {
        $userName = 'Fulano da Silva';
        $verificationCode = '123456';
        $bannerUrl = 'https://30semanas.com.br/assets/img/banner.jpg';
        $events = 'Ciclo do 30 Semanas 2025';
        $clienteNome = 'Equipe 30 Semanas';

        return view('emails.passwordChanged', compact('userName', 'verificationCode', 'bannerUrl', 'events', 'clienteNome'));
    });
});
