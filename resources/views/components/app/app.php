<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="<?php echo '../icons/window/icono.svg'; ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap">
    <link rel="stylesheet" href="<?php echo '../css/components/app/app.css'; ?>">
    <?php if (!isset($css_template)): ?>
        <link rel="stylesheet" href="<?php echo '../css/components/app/content.css'; ?>">
    <?php endif; ?>

    <?php
    if (isset($css)) {
        echo $css;
    }
    ?>

    <title>Perez Lara Cia Ltda - <?php
        if (isset($title)) {
            echo $title;
        } ?></title>
</head>
<body class="scrollbar">
<div class="container">
    <?php
    require_once 'header.php';
    ?>

    <?php
    require_once 'sidebar.php';
    ?>

    <main class="main">
        <?php
        if (!isset($content)) {
            require_once 'header_content.php';
            require_once 'content.php';
        } elseif (!isset($not_content)) {
            require_once '../content/' . $content;
        }
    ?>
    </main>
</div>
<script src="<?php echo '../../../js/sidebar.js'; ?>"></script>
<script src="<?php echo '../../../js/modal.js'; ?>"></script>
<?php
if (isset($js)) {
    echo $js;
}
?>

<?php if (!isset($content)): ?>
<script src="<?php echo '../../../js/input.js'; ?>"></script>
<script src="<?php echo '../../../js/modal_static.js'; ?>"></script>
<script src="<?php echo '../../../js/context_menu.js'; ?>"></script>
<script src="<?php echo '../../../../Helpers/helper.js'; ?>"></script>
<script src="<?php echo '../../../../Helpers/helper_form.js'; ?>"></script>
<script src="<?php echo '../../../js/cloud.js'; ?>"></script>
<script src="<?php echo '../../../js/table.js'; ?>"></script>
<?php endif; ?>

<?php if (isset($filters)): ?>
<script src="<?php echo '../../../js/search.js'; ?>"></script>
<script src="<?php echo '../../../js/calendar.js'; ?>"></script>
<?php endif; ?>

</body>
</html>
