<?php

$title = 'Pólizas';
$css = '<link rel="stylesheet" href="' . css('policies.css') . '">';
$js = '<script src="' . js('actions/policy.js') . '"></script>'.
      '<script src="' . js('input_file.js') . '"></script>'.
      '<script src="' . view('components/form_elements/dropdown/filter_select.js') . '"></script>';
$modal = 'poliza_modal.php';
$pdf = 'polizas/pdf';
$icon_view = 'sidebar/policies.svg';
$filters = [
    ['Fecha'],
    ['Seguro'],
    ['Aseguradora'],
    ['Estado', $status],
    [
        'Valor asegurado',
        [
            ['primer', '$0 - $2000000'],
            ['segundo', '$2000001 - $1000000'],
            ['tercero', '$10000001 - $50000000'],
            ['cuarto', '+ $50000000']
        ]
    ],
    [
        'Valor prima',
        [
            ['primer', '$0 - $2000000'],
            ['segundo', '$2000001 - $1000000'],
            ['tercero', '$10000001 - $50000000'],
            ['cuarto', '+ $50000000']
        ]
    ],
];
$title_modal_confirm = '¿Desea eliminar la póliza?';
require_once view('components/app/app.php');
