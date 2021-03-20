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
    $form->add(BrickForm\PasswordField::class, ['toconfirm']);

    $form->getElementByName('username')->addCustomValidator('test1');
    $form->getElementByName('username')->addCustomValidator('test2');

    echo $form->getView();

    ?>

</body>

<script>
    var brickform_json_data = <?php echo BrickForm\Form::getCustomValidatorsAsJSON(); ?>
</script>
<script type="text/javascript" src="testfunc.js" defer></script>
<script type="text/javascript" src="brickform-validations.js" defer>

</script>

</html>