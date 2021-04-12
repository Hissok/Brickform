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
    $form->add(BrickForm\Field::class);
    $form->add(BrickForm\PasswordField::class, ['toconfirm', 'atleast_3_number', 'nospace']);

    echo $form->getView();

    ?>

</body>

<script type="text/javascript" src="brickform-validations.js" defer>

</script>

</html>