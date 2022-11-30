<?php

namespace App\Controllers;

use App\Models\File;
use App\Models\Sinister;
use App\Traits\Controllers\InsurerInsuranceActive;
use App\Traits\Controllers\PDF;
use Exception;

class SinisterController extends Controller
{
    use InsurerInsuranceActive;
    use PDF;

    public function index(): bool|array
    {
        return (new Sinister()) -> index();
    }

    public function create(Sinister $sinister): void
    {
        try {
            $upload_files = $sinister -> upload();
            if (is_string($upload_files)) {
                throw new Exception($upload_files);
            }
            $new_sinister = $sinister -> create();
            if (is_string($new_sinister)) {
                throw new Exception($new_sinister);
            }
            $id_sinister = (new Sinister($new_sinister));
            foreach ($upload_files as $file) {
                $new_file = (new File($file['id'], $file['name'], $id_sinister)) -> create();
                if (is_string($new_file)) {
                    throw new Exception($new_file);
                }
            }
            $this -> response('Siniestro registrado');
        } catch(Exception $e) {
            echo json_encode($e -> getMessage());
        }
    }

    public function show(Sinister $sinister): void
    {
        echo json_encode($sinister -> show());
    }

    public function data(): void
    {
        $array = $this -> productsActive();
        $array['claims'] = $this -> index();
        echo json_encode($array);
    }

    public function view(string $view): void
    {
        $status = (new Sinister()) -> estado();
        require_once $view;
    }

    public function pdf()
    {
        $this -> createPDF($this -> index(), 'claims/pdf.php', 'siniestros.pdf');
    }

    public function update(Sinister $sinister, ?array $files_delete): void
    {
        try {
            if ($files_delete !== null) {
                foreach ($files_delete as $file_delete) {
                    $deleted_file_service = $file_delete -> delete();
                    if ($deleted_file_service !== true) {
                        throw new Exception($deleted_file_service);
                    }
                    $deleted_file = (new File($file_delete -> getId())) -> delete();
                    if ($deleted_file !== true) {
                        throw new Exception($deleted_file);
                    }
                }
            }
            $sinister_update = $sinister -> update();
            if (is_string($sinister_update)) {
                throw new Exception($sinister_update);
            }
            if ($sinister -> verifyFileService()) {
                $uploaded_files = $sinister -> upload();
                if (is_string($uploaded_files)) {
                    throw new Exception($uploaded_files);
                }
                foreach ($uploaded_files as $uploaded_file) {
                    $created_file = (new File($uploaded_file['id'], $uploaded_file['name'], $sinister)) -> create();
                    if (is_string($created_file)) {
                        throw new Exception($created_file);
                    }
                }
            }
            $this -> response('Siniestro actualizado');
        } catch (Exception $e) {
            echo json_encode($e -> getMessage());
        }
    }

    public function delete(Sinister $sinister): void
    {
        $sinister -> delete();
        $this -> response('Siniestro eliminado');
    }
}
