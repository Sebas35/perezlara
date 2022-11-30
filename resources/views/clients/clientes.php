<?php

$title = 'Clientes';
$css = '<link rel="stylesheet" href="' . css('clients.css') . '">';
$js = '<script src="' . js('actions/helper/UserClient.js') . '"></script>' .
      '<script src="' . js('actions/client.js') . '"></script>';
$modal = 'customers_modal.php';
$pdf = 'clientes/pdf';
$icon_view = 'sidebar/clients.svg';
$filters = [['Departamento', $departments], ['Ciudad', $cities], ['Estado', $status],];
$title_modal_confirm = '¿Desea eliminar el cliente?';
require_once view('components/app/app.php');
