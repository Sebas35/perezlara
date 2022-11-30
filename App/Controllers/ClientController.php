<?php

namespace App\Controllers;

use App\Models\Client;
use App\Traits\Controllers\PDF;
use Dompdf\Dompdf;
use Exception;

class ClientController extends Controller
{
    use PDF;

    public function index(): bool|array
    {
        return (new Client()) -> index();
    }

    public function create(Client $client): void
    {
        try {
            $created = $client -> create();
            if (is_string($created)) {
                throw new Exception($created);
            }
            $this -> response('Cliente registrado');
        } catch (Exception $e) {
            echo json_encode($e -> getMessage());
        }
    }

    public function show(Client $client, bool $all = true): void
    {
        echo json_encode($client -> show($all));
    }

    public function view(string $view): void
    {
        $client = (new Client());
        $departments = $client -> department();
        $cities = $client -> city();
        $status = $client -> state();
        $types_documents = $client -> documentType();
        require_once $view;
    }

    public function filter(array ...$array): void
    {
        echo json_encode((new Client()) -> filter(...$array));
    }

    public function search(string|int $search): void
    {
        echo json_encode((new Client()) -> search($search));
    }

    public function pdf()
    {
        $this -> createPDF($this -> index(), 'clients/pdf.php','clientes.pdf');
    }

    public function update(Client $client, Client $client_update): void
    {
        $client -> update($client_update -> getDocument());
        $this -> response('Cliente actualizado');
    }

    public function delete(Client $client): void
    {
        $client -> delete();
        $this -> response('Cliente eliminado');
    }
}
