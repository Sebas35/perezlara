<?php
$title = 'Usuarios';
$filters = [['Rol',$roles]];
$modal = 'user_modal.php';
$icon_view = 'sidebar/clients.svg';
$js = '<script src="' . js('actions/helper/UserClient.js') . '"></script>' .
      '<script src="'.js('actions/users.js').'"></script>';
$title_modal_confirm = '¿Desea eliminar el usuario?';
require_once view('components/app/app.php');