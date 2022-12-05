<?php

$title = 'Dashboard';
$css_template = false;
$css = '<link rel="stylesheet" href="' . ('../css/dashboard.css') . '">';
$js = '<script src="' . ('../js/cloud.js') . '"></script>' .
$js = '<script src="' . ('../js/actions/dashboard.js') . '"></script>' .
      '<script src="' . ('../js/input.js') . '"></script>' .
      '<script src="' . ('components/form_elements/dropdown/filter_select.js') . '"></script>'.
      '<script src="' . ('../js/table.js') . '"></script>';
$content = 'main_dashboard.php';
$group = 'search.php';
$filters = [['Seguro'], ['Aseguradora'],];
$buttons_filters = false;
require_once ('components/app/app.php');
