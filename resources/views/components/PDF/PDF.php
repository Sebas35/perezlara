<?php
$keys = array_keys($table[0]);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="<?php echo css('PDF/pdf.css'); ?>">
    <title><?php
        if (isset($title)) {
            echo $title;
        } ?></title>
</head>
<body>
<table class="table">
    <thead class="thead">
    <tr>
        <?php
        for($i = 0; $i < count($keys); $i++){
            echo '<th>'.$keys[$i].'</th>';
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach ($table as $item) {
        echo '<tr class="tr">';
        for($i = 0; $i < count($keys); $i++){
            echo '<td class="td">';
            if (in_array($keys[$i],['Aseguradora','Foto'])) {
                foreach (explode(',',$item[$keys[$i]]) as $value) {
                    echo '<img class="img-table" src="'.url_base().$value.'" alt="">';
                }
            } else {
                echo $item[$keys[$i]];
            }
            echo '</td>';
        }
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
</body>
</html>