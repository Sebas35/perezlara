<?php

use App\Controllers\ClientController;
use App\Controllers\InsuranceController;
use App\Controllers\InsurerController;
use App\Controllers\PolicyController;
use App\Controllers\QuoteController;
use App\Controllers\SinisterController;
use App\Controllers\UserController;

return [
    'login' => [
        UserController::class,
        ['login']
    ],
    'dashboard' => 'dashboard',
    'clientes' => [
        ClientController::class,
        ['clients/clientes']
    ],
    'cotizaciones' => [
        QuoteController::class,
        ['quotes/cotizaciones']
    ],
    'polizas' => [
        PolicyController::class,
        ['policies/polizas']
    ],
    'siniestros' => [
        SinisterController::class,
        ['claims/siniestros']
    ],
    'perfil' => [
        UserController::class,
        ['perfil']
    ],
    'usuarios' => [
        UserController::class,
        ['users/usuarios','users']
    ],
    'aseguradoras' => InsurerController::class,
    'seguros' => InsuranceController::class,
    'productos' => 'productos',
];
