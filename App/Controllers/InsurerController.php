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
            $file_service = $insurer -> upload();
            if (is_string($file_service)) {
                throw new Exception('No se pudo cargar el archivo. ' . $file_service);
            }
            $insurer -> setFileService($file_service['id']);
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
