/** Do Not modify following content */

const brickform_submits = document.querySelectorAll('.brickform-submit');
var brickform_X = 0;
var brickform_error_count = 0;
var errors = [];

/*************************************** */

/**
 * If you want to change the error messages: Indexes correspond to the validator code
 * 
 * for the atleast errors, change the 'atleast_X', then the type from 'number' to 'character'
 *  */
const error_messages = {
    'atleast_X': "La valeur doit contenir au moins ",
    'nospace': "La valeur ne doit pas contenir d'espaces",
    'email_format': "L'email entrée est invalide",
    'toconfirm': "Les deux valeurs ne correspondent pas",
    'required': "Veuillez remplir ce champs",

    'number': "chiffres",
    'specialchar': "caractères spéciaux",
    'uppercase': "majuscules",
    'lowercase': "minuscules",
    'character': "caractères"
}

brickform_submits.forEach(function (submit) {
    submit.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelectorAll('.brickform-form-group').forEach((group) => {
            group.classList.remove('error');
        })

        document.querySelectorAll('.brickform-errors').forEach((errors) => {
            errors.innerHTML = '';
        });

        var id = this.id.substring(this.id.length - 1);
        const fields = document.querySelector('#brickform-form-' + id).querySelectorAll('.brickform-field');

        fields.forEach(function (field) {
            const validators = field.dataset.validators.split(" ");
            validators.forEach(function (validator) {

                if (validator === 'toconfirm') {
                    toConfirm(field);
                } else if (validator === 'nospace') {
                    noSpace(field);

                } else if (validator.startsWith('atleast')) {
                    atLeast(field, validator);
                } else if (validator === 'email_format') {
                    emailFormat(field.id);
                } else if (validator === 'required') {
                    required(field)
                }
            })

            // custom validators
            if (typeof (brickform_json_data) !== "undefined" && field.id in brickform_json_data) {

                for (var i = 0; i < brickform_json_data[field.id].length; i++) {

                    console.log(brickform_json_data[field.id][i]);
                    brickform_error_count++;

                    const result = window[brickform_json_data[field.id][i]](field);
                    const valid = result[0];

                    if (!valid) {
                        document.querySelector('#brickform-group-' + field.id).classList.add('error');
                        errors.push(result[1]);
                        brickform_error_count++;
                    }
                }

            }

            // add errors to view

            var html = "";

            errors.forEach(function (error) {
                html += '<li>' + error + '</li>';
            })
            document.querySelector('#brickform-group-' + field.id).querySelector('.brickform-errors').innerHTML = html;
            errors = [];
        })

        // else submit

        if (!brickform_error_count)
            document.querySelector('#brickform-form-' + id).submit();
    })
})

const brickform_number_fields = document.querySelectorAll('.brickform-number');
console.log(brickform_number_fields.length)
brickform_number_fields.forEach(function (field) {
    field.value = field.min;
    console.log(field.max);
    field.addEventListener('input', function () {

        if (isNaN(field.value) || field.value < field.min) {
            field.value = field.min;
        } else if (parseInt(field.value) > field.max) {
            field.value = field.max;
        }

    })
})

/**** Do not modify following content */

// toconfirm validator
const toConfirm = function (field) {
    const brickform_confirm = document.querySelector('#' + field.id + '-confirm');
    if (brickform_confirm.value !== field.value) {
        document.querySelector('#brickform-group-' + brickform_confirm.id).classList.add('error');
        document.querySelector('#brickform-group-' + brickform_confirm.id).querySelector('.brickform-errors').innerHTML = '<li>' + error_messages['toconfirm'] + '<li>';
        console.log(document.querySelector('#brickform-group-' + brickform_confirm.id).querySelector('.brickform-errors'));
        brickform_error_count++;
    }
}

//no_space validator
const noSpace = function (field) {
    if (field.value.split(' ').length > 1) {
        document.querySelector('#brickform-group-' + field.id).classList.add('error');
        errors.push(error_messages['nospace']);
        brickform_error_count++;
    }
}

//atLeast validators
const atLeast = function (field, validator) {
    var min = validator.split('_')[1];
    var chartype = validator.split('_')[2];
    var quota = 0;

    switch (chartype) {
        case 'number':
            for (var i = 0; i < field.value.length; i++) {
                if (!isNaN(field.value.charAt(i)))
                    quota++;
            }
            break;

        case 'specialchar':
            for (var i = 0; i < field.value.length; i++) {
                if (field.value.charAt(i).match(/[~`!#$%\^&*+=\-\[\]\\';,/{}|\\":<>\?]/g))
                    quota++;
            }
            break;

        case 'lowercase':
            for (var i = 0; i < field.value.length; i++) {
                if (field.value.charAt(i) == field.value.charAt(i).toLowerCase())
                    quota++;
            }
            break;

        case 'uppercase':
            for (var i = 0; i < field.value.length; i++) {
                if (field.value.charAt(i) == field.value.charAt(i).toUpperCase())
                    quota++;
            }
            break;

        case 'character':
            quota = field.value.length;
            break;

        default: break;
    }

    if (quota < min) {
        document.querySelector('#brickform-group-' + field.id).classList.add('error');
        brickform_X = min;
        errors.push(error_messages['atleast_X'] + ' ' + brickform_X + ' ' + error_messages[chartype]);
        brickform_error_count++;
    }
}

//email_format
const emailFormat = function (field) {
    const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    if (!field.value.match(re)) {
        document.querySelector('#brickform-group-' + field.id).classList.add('error');
        errors.push(error_messages['email_format']);
        brickform_error_count++;
    }
}

//required validator

const required = function (field) {
    if (field.value.length == 0) {
        document.querySelector('#brickform-group-' + field.id).classList.add('error');
        errors.push(error_messages['required']);
        brickform_error_count++;
    }
}

/********** */