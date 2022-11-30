<?php

namespace App\Controllers;

use App\Models\Client;
use App\Models\File;
use App\Models\Policy;
use App\Models\Quote;
use App\Traits\Controllers\InsurerInsuranceActive;
use App\Traits\Controllers\PDF;
use Exception;

class PolicyController extends Controller
{
    use InsurerInsuranceActive;
    use PDF;

    public function index() :bool|array
    {
        return (new Policy()) -> index();
    }

    public function create(Policy $policy) :void
    {
        try {
            $file_service = $policy -> upload();
            if (is_string($file_service)) {
                throw new Exception('No se pudo cargar el archivo. ' . $file_service);
            }
            $new_policy = $policy -> create();
            if ($new_policy !== true) {
                throw new Exception($new_policy);
            }
            $file = (new File($file_service['id'], $file_service['name'], new Policy($policy -> getCode()))) -> create(
            );
            if (is_string($file)) {
                throw new Exception($file);
            }
            $this -> response('Póliza creada');
        } catch (Exception $e) {
            echo json_encode($e -> getMessage());
        }
    }

    public function show(Policy $policy, bool $all = true) :void
    {
        if ($all) {
            $show_policy = $policy -> show();
            echo json_encode([
                'quotes' => (new Quote((new Client($show_policy['Documento'])))) -> accordingClient($policy),
                'policy' => $show_policy
            ]);
        }else {
            echo json_encode($policy -> show($all));
        }
    }

    public function data() :void
    {
        $array = $this -> productsActive();
        $array['policies'] = $this -> index();
        echo json_encode($array);
    }

    public function view(string $view) :void
    {
        $status = (new Policy()) -> state();
        require_once $view;
    }

    public function pdf()
    {
        $this -> createPDF($this -> index(), 'policies/pdf.php', 'polizas.pdf');
    }

    public function update(Policy $policy, Policy $policy_update) :void
    {
        try {
            $update_policy = $policy -> update($policy_update -> getCode());
            if (is_string($update_policy)) {
                throw new Exception($update_policy);
            }
            if ($policy -> verifyFileService()) {
                $updated_file = $policy -> updateFile();
                if (is_string($updated_file)) {
                    throw new Exception('No se pudo actualizar el archivo de la póliza');
                }
                $file = (new File($updated_file['id'], $updated_file['name'])) -> update();
                if (is_string($file)) {
                    throw new Exception($file);
                }
            }
            $this -> response('Póliza actualizada');
        } catch (Exception $e) {
            echo json_encode($e -> getMessage());
        }
    }

    public function delete(Policy $policy) :void
    {
        $policy -> delete();
        $this -> response('Póliza eliminada');
    }
}
