<!DOCTYPE html>

<?php require('brickform.php'); ?>

<html>

<head>
    <meta charset='utf-8'>
    <link rel="stylesheet" href="brickform.css">
</head>

<body>

    <?php

    // On créé le formulaire
    $form = new BrickForm\Form();
    // On définit son chemin de redirection
    $form->setAction('#');
    // On lui applique la méthode POST (insensible à la casse, GET par défaut)
    $form->setMethod('post');

    /**
     *  On lui ajoute un input text
     *  auquel on applique 'name="username"'
     *  Son label contiendra "Nom d'utilisateur"
     *  On lui ajoute les classes 'field' et 'input-field' (Ce sont des exemples indépendants de ma librairie)
     * */
    $form->add(BrickForm\Field::class, [], 'username', "Nom d'utilisateur", ['field', 'input-field']);

    /**
     * On inclue également un input password avec les contraintes suivantes :
     *      - Champs requis
     *      - A confirmer (cela créé une copie du champ avec les mêmes attributs qui a pour contrainte la nécessité d'avoir une valeur
     *          égale au champ qui lui est lié)
     *      - Au moins 8 caractères
     *      - Au moins 3 chiffres
     *      - Au moins 2 caractères spéciaux
     *      - Au moins une minuscule
     *      - Au moins une majuscule
     *      - Pas d'espace
     * 
     *      (Le choix du nombre dans les contraintes 'atleast' est arbitraire)
     */

    $form->add(BrickForm\PasswordField::class, [
        'required',
        'toconfirm', 'atleast_8_character', 'atleast_3_number', 'atleast_2_specialchar', 'atleast_1_lowercase', 'atleast_1_uppercase', 'nospace'
    ]);

    // On ajoute un validator personnalisé a l'input dont le nom est 'username'. Le paramètre est le nom de la fonction (celle-ci est définie plus bas)
    $form->getElementByName('username')->addCustomValidator('isCapitalized');

    // On modifie le texte du submit et on lui ajoute des classes (Par défaut, le texte est 'Envoyer')
    $form->configSubmit('Connexion !', ['btn, btn-primary']);
    // On affiche le formulaire
    echo $form->getView();

    // Action après soumission du formulaire
    if ($form->isSubmitted()) {
        echo "<p style='color: green; font-weight: bold; font-size: 1.3em;'>Hello $_POST[username] !</p>";
    }

    ?>

</body>

<!-- Ce script permet d'utiliser des validators personnalisée.
     Appelez-le une seule fois pour tous les formulaires de la page -->
<script defer>
    var brickform_json_data = <?php echo BrickForm\Form::getCustomValidatorsAsJSON(); ?>
</script>

<!-- Ce script est un validateur personnalisé que la première lettre de la valeur de l'input est une majuscule -->
<script defer>
    function isCapitalized(field) {
        /**
         * Si la contrainte est respectée, on renvoie un tableau avec la valeur TRUE, sinon,
         * on renvoie un tableau avec la valeur FALSE et le message d'erreur associé
         * 
         * Veillez à respecter ce principe
         * 
         * Vous pouvez biensure écrire ce script dans un fichier JS et l'inclure à cette page
         */
        return (field.value && field.value[0].toUpperCase() === field.value[0] ? [1] : [0, 'Ce pseudo doit commencer par une majuscule']);
    }
</script>

<!-- Fichier JS qui gère les validators. la variable 'error_messages' au début de ce fichier contient les messages d'erreurs associés
     aux validateurs. Vous êtes libres de modifier ces messages -->
<script type="text/javascript" src="brickform-validations.js" defer>

</script>

</html>