<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/preview-email', function () {
    $userName = 'Fulano da Silva';
    $verificationCode = '123456';
    $bannerUrl = 'https://30semanas.com.br/assets/img/banner.jpg';
    
    return view('emails.verify', compact('userName', 'verificationCode', 'bannerUrl'));
});