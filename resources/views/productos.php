<?php

$title = 'Productos';
$css_template = false;
$css = '<link rel="stylesheet" href="' . css('products.css') . '">';
$js = '<script src="' . js('modal_static.js') . '"></script>' .
$js = '<script src="' . js('drive.js') . '"></script>' .
      '<script src="' . js('input.js') . '"></script>' .
      '<script src="' . js('actions/products.js') . '"></script>' .
      '<script src="' . js('context_menu.js') . '"></script>' .
      '<script src="' . helper_js('helper.js') . '"></script>' .
      '<script src="' . helper_js('helper_form.js') . '"></script>' .
      '<script src="' . view('components/form_elements/dropdown/filter_select.js') . '"></script>';
$content = 'main_products.php';

require_once view('components/app/app.php');
