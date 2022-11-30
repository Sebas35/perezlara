<?php

namespace App\Controllers;

use App\Models\Coverage;
use App\Models\Quote;
use App\Traits\Controllers\InsurerInsuranceActive;
use App\Traits\Controllers\PDF;
use Exception;

class QuoteController extends Controller
{
    use InsurerInsuranceActive;
    use PDF;

    public function index() :bool|array
    {
        return (new Quote()) -> index();
    }

    public function option(Quote $quote) :void
    {
        echo json_encode($quote -> option());
    }

    public function create(Quote $quote) :void
    {
        $quote -> create();
        $this -> response('Cotización registrada');
    }

    public function show(Quote $quote) :void
    {
        echo json_encode($quote -> show());
    }

    public function accordingClient(Quote $quote): void
    {
        try {
            $client_quotes = $quote -> accordingClient();
            if (empty($client_quotes)) {
                throw new Exception('El cliente no tiene cotizaciones');
            }
            echo json_encode($client_quotes);
        } catch (Exception $e) {
            echo json_encode($e -> getMessage());
        }
    }

    public function view(string $view) :void
    {
        $status = (new Quote()) -> estado();
        require_once $view;
    }

    public function data() :void
    {
        $array = $this -> productsActive();
        $array['coverages'] = (new Coverage()) -> index();
        $array['quotes'] = $this -> index();
        echo json_encode($array);
    }

    public function pdf()
    {
        $this -> createPDF($this -> index(), 'quotes/pdf.php', 'cotizaciones.pdf');
    }

    public function filter(array ...$array) :void
    {
        echo json_encode((new Quote()) -> filter(...$array));
    }

    public function update(Quote $quote) :void
    {
        $quote -> update();
        $this -> response('Cotización actualizada');
    }

    public function delete(Quote $quote) :void
    {
        $quote -> delete();
        $this -> response('Cotización eliminada');
    }
}
