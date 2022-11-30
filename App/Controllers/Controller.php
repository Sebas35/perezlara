<?php

namespace App\Controllers;

abstract class Controller
{
    abstract public function index();

    public function response(string $msg, array|null $data = null): void
    {
        echo json_encode(['data' => $data ?? $this -> index(), 'msg' => $msg]);
    }
}
