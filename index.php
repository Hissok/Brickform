<!DOCTYPE html>

<?php require('brickform.php'); ?>

<html>

<head>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="brickform.css">
</head>

<body>

    <?php

    $form = new BrickForm\Form();
    $form->add(BrickForm\NumberField::class, ['min:8', 'max:1000']);

    echo $form->getView();

    ?>

</body>

<script type="text/javascript" src="brickform-validations.js" defer></script>

</html>