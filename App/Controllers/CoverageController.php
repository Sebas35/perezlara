<?php

namespace App\Controllers;

use App\Models\Coverage;

class CoverageController extends Controller
{
    public function index(): bool|array
    {
        return (new Coverage()) -> index();
    }

    public function create(Coverage $coverage): void
    {
        $coverage -> create();
        $this -> response('Cobertura creada');
    }

    public function show(Coverage $coverage): void
    {
        echo json_encode($coverage -> show());
    }

    public function update(Coverage $coverage): void
    {
        $coverage -> update();
        $this -> response('Cobertura actualizada');
    }

    public function delete(Coverage $coverage): void
    {
        $coverage -> delete();
        $this -> response('Cobertura eliminada');
    }
}
