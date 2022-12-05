<?php

namespace App\Controllers;

use App\Models\Insurance;
use Exception;

class InsuranceController extends Controller
{
    public function index(): bool|array
    {
        return (new Insurance()) -> index();
    }

    public function create(Insurance $insurance): void
    {
        try {
            $new_insurance = $insurance -> create();
            if ($new_insurance !== true) {
                throw new Exception($new_insurance);
            }
            $this -> response('Seguro registrado', $insurance -> card());
        } catch (Exception $e) {
            echo json_encode($e -> getMessage());
        }
    }

    public function show(Insurance $insurance): void
    {
        echo json_encode($insurance -> show());
    }

    public function update(Insurance $insurance): void
    {
        $insurance -> update();
        $this -> response('Seguro actualizado');
    }

    public function delete(Insurance $insurance): void
    {
        $insurance -> delete();
        $this -> response('Seguro eliminado');
    }
}
