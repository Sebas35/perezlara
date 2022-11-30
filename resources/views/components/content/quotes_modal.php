<?php

require_once view('components/form_elements/input.php');
require_once view('components/form_elements/dropdown/select.php');
?>
<div id="container-form" class="container-form">
    <form id="modal-form" class="form scrollbar" aria-labelledby="title-form">
        <div class="header-modal">
            <h1 id="title-form" class="title-form">Nueva cotización</h1>
            <img class="close-modal" data-dismiss="modal2" aria-label="Cerrar"
                 src="<?php
                 echo icon('buttons/closeDark.svg') ?>" alt="">
        </div>
        <fieldset class="fieldset">
            <legend class="legend">Información cliente</legend>
            <div class="body-modal">
                <?php
                input('Número de documento');
input('Cliente', 'text', null, null, 'disabled');
?>
            </div>
        </fieldset>
        <fieldset class="fieldset fieldset-table">
            <legend class="legend">Información cotización</legend>
            <div class="body-modal">
                <?php
                select('Seguro');
                select('Aseguradora', null,true);
                select('Coberturas', null,  true);
                input('Valor asegurado');
                ?>
            </div>
            <span id="message-empty-table" class="message-empty-table">Seleccione opciones para crear cotización</span>
            <div class="container-table-quote">
                <table id="table-quote" class="table-quote scrollbar">
                    <thead id="thead-quote" class="thead-quote">
                        <tr id="titles-quote" class="is-hidden">
                            <th rowspan="2">Cobertura</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-quote" class="tbody-quote">
                    </tbody>
                    <tfoot id="tfoot-quote" class="tfoot-quote is-hidden">
                    <tr>
                        <th>Valor a pagar</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </fieldset>
        <div class="footer-modal">
            <button id="clean-form" type="reset" class="primary-button button-is-black">Limpiar formulario</button>
            <button id="send-form" type="submit" class="primary-button button-is-red">Registrar cotización</button>
        </div>
    </form>
</div>