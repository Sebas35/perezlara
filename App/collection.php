<?php

use App\Controllers\SinisterController;
use App\Models\Insurance;
use App\Models\Insurer;
use App\Models\Sinister;
use Storage\DriveFile;

return [
    Sinister::class => [
        ['__construct6','__construct7'],
        DriveFile::class
    ],
    SinisterController::class => [
        ['update'],
        DriveFile::class
    ],
    Insurer::class => [
        ['__construct3','__construct4'],
        Insurance::class
    ],
    Insurance::class => [
        ['__construct3','__construct4'],
        Insurer::class
    ],
];
