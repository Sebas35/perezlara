<?php

require_once helper('transform.php');

function input(
    string $input,
    string $type = 'text',
    ?string $id_ = null,
    ?string $id_group = null,
    $optional = null
): void {
    $id = $id_ ?? separate($input);
    $name = 'name=' . $id;
    if ($id_group !== null) {
        $id_group = 'id=' . $id_group;
    }
    echo "<div $id_group class='group-input'>
                <div class='inputs'>";
    echo $type !== 'textarea'
        ? "<input id='$id' $name class='input' type='$type' $optional>"
        : "<textarea id='$id' $name class='input scrollbar' $optional></textarea>";
    echo "<label id='label-$id' for='$id' class='label'>$input</label>
                </div>
                <span role=alert class=alert></span>
            </div>";
}

function input_file(string|null $optional = null, bool $filename = false): void
{
    if ($filename) {
        echo '<div class="container-file">';
    }
    echo "<label class='label-file' for='file'>
                Cargar archivo
                <input id='file' type='file' class='file' $optional>
          </label>";
    if ($filename) {
        echo '<span id="filename" class="filename"></span>
              <span id="pop-filename" class="title pop-filename"></span>
              </div>';
    }
}
