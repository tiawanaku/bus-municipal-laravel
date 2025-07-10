<?php

return [
    'otp_code' => 'OTP Code',

    'mail' => [
        'subject' => 'OTP Code',
        'greeting' => 'Hola!',
        'line1' => 'Tu código OTP es: :code',
        'line2' => 'Este código es valido por :seconds segundos.',
        'line3' => 'Si no solicitaste el código, por favor ignora este mensaje.',
         'line4' => 'La solicitud de este acceso se originó desde la dirección IP :ip',
        'salutation' => 'Saludos, :app_name',
    ],

    'view' => [
        'time_left' => 'segundos restantes',
        'resend_code' => 'Reenviar código',
        'verify' => 'Verificar',
        'go_back' => 'Regresar',
    ],

    'notifications' => [
        'title' => 'Código OTP enviado',
        'body' => 'El código de verificación ha sido enviado a tu dirección de correo electrónico. Será válido por :seconds segundos.',
    ],

    'validation' => [
        'invalid_code' => 'El código que ingresaste es inválido.',
        'expired_code' => 'El código que ingresaste ha expirado.',
    ],
];
