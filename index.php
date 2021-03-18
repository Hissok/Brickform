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
    $form->add(BrickForm\Field::class, ['required']);
    $form->add(BrickForm\PasswordField::class, [
        'required', 'atleast_2_number', 'atleast_1_specialchar',
        'atleast_8_character', 'toconfirm'
    ]);

    echo $form->getView();

    ?>

</body>

<script type="text/javascript" src="brickform-validations.js" defer></script>

</html>