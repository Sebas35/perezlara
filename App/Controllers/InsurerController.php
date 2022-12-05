<?php

namespace App\Controllers;

use App\Models\File;
use App\Models\Insurer;
use Exception;

class InsurerController extends Controller
{
    public function index(): bool|array
    {
        return (new Insurer()) -> index();
    }

    public function create(Insurer $insurer): void
    {
        try {
            $cloud_file = $insurer -> upload();
            if (is_object($cloud_file)) {
                throw new Exception('No se pudo cargar el archivo. ' . $cloud_file -> getMessage());
            }
            $insurer -> setLogo($cloud_file);
            $new_insurer = $insurer -> create();
            if ($new_insurer !== true) {
                throw new Exception($new_insurer);
            }
            $this -> response('Aseguradora registrada', $insurer -> card());
        } catch (Exception $e) {
            echo json_encode($e -> getMessage());
        }
    }

    public function show(Insurer $insurer): void
    {
        echo json_encode($insurer -> show());
    }

    public function update(Insurer $insurer): void
    {
        $insurer -> update();
        $this -> response('Aseguradora actualizada');
    }

    public function delete(Insurer $insurer): void
    {
        $insurer -> delete();
        $this -> response('Aseguradora eliminada');
    }
}
