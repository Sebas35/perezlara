<?php

$title = 'Cotizaciones';
$css = '<link rel="stylesheet" href="' . css('quotes.css') . '">';
$js =  '<script src="' . js('actions/quote.js') . '"></script>'.
       '<script src="' . view('components/form_elements/dropdown/filter_select.js') . '"></script>';
$modal = 'quotes_modal.php';
$pdf = 'cotizaciones/pdf';
$icon_view = 'sidebar/quotes.svg';
$filters = [['Fecha'], ['Seguro'], ['Aseguradora'], ['Estado', $status],];
$title_modal_confirm = '¿Desea eliminar la cotización?';
require_once view('components/app/app.php');
