<?php

$title = 'Dashboard';
$css_template = false;
$css = '<link rel="stylesheet" href="' . ('http://localhost/perezlara/resources/css/dashboard.css') . '">';
$js = '<script src="' . ('http://localhost/perezlara/resources/js/cloud.js') . '"></script>' .
$js = '<script src="' . ('http://localhost/perezlara/resources/js/actions/dashboard.js') . '"></script>' .
      '<script src="' . ('http://localhost/perezlara/resources/js/input.js') . '"></script>' .
      '<script src="' . ('http://localhost/perezlara/resources/view/components/form_elements/dropdown/filter_select.js') . '"></script>'.
      '<script src="' . ('http://localhost/perezlara/resources/js/table.js') . '"></script>';
$content = 'main_dashboard.php';
$group = 'search.php';
$filters = [['Seguro'], ['Aseguradora'],];
$buttons_filters = false;
require_once ('http://localhost/perezlara/resources/view/components/app/app.php');
