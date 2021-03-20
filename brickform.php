<?php

namespace BrickForm;

class Form
{

    public static $INSTANCE_COUNT = 0;
    public static $INSTANCES = array();

    private $item_count = 0;
    private $instance_id;

    private $submit;

    private $components = array();
    private $id;
    private $classes = array();
    private $method = 'GET';
    private $action = '#';

    public function __construct()
    {
        self::$INSTANCE_COUNT++;
        $this->instance_id = self::$INSTANCE_COUNT;
        $this->id = $this->instance_id;
        self::$INSTANCES[] = $this;
        $this->submit = "<div class='brickform-button-group'><button type='submit' class='brickform-submit' id='brickform-submit-" . $this->instance_id . "'>Valider</button></div>";
    }

    public function getId(): int
    {
        return $this->instance_id;
    }

    public function setMethod(?string $method)
    {
        $this->method = strtoupper($method);
    }

    public function setAction(?string $action)
    {
        $this->action = $action;
    }

    public function add($component_class, $validators = [], $name = null, $label_text = null, $classes = [])
    {
        $this->components[] = $component_class::create($this, $validators, $name, $label_text, $classes);
    }

    public function configSubmit($text, $classes = [])
    {
        $this->submit = "<div class='brickform-button-group'><button type='submit' id='brickform-submit-" . $this->instance_id . "' class='brickform-submit ";
        foreach ($classes as $class) {
            $this->submit .= " " . $class;
        }

        $this->submit .= "'>" . $text . "</button></div>";
    }

    public function incrementItemCount()
    {
        $this->item_count++;
    }
    public function getItemCount()
    {
        return $this->item_count;
    }

    public static function getCustomValidatorsAsJSON()
    {

        $json = array();

        foreach (self::$INSTANCES as $instance) {
            foreach ($instance->components as $component) {
                if (!empty($component->getCustomValidators())) {
                    $array = [];
                    foreach ($component->getCustomValidators() as $func_name) {
                        $array[] = $func_name;
                    }
                    $json[$component->getId()] = $array;
                }
            }
        }

        return json_encode($json);
    }

    public function getView()
    {
        $html = "<form action='$this->action' method='$this->method' id='brickform-form-$this->id' class='brickform-form ";

        foreach ($this->classes as $class) {
            $html .= $class . ' ';
        }

        $html .= "'>\n\t";

        foreach ($this->components as $component) {
            $html .= $component->getView() . "\n";

            if (in_array('toconfirm', $component->getValidators())) {
                $html .= $component->getAsConfirmation();
            }
        }

        $html .= $this->submit;

        $html .= "</form>";

        return $html;
    }

    public function getElementByName(?string $name)
    {
        foreach ($this->components as $component) {
            if ($component->getName() === $name)
                return $component;
        }

        return null;
    }
}

abstract class FormComponent
{
    protected $label_text;
    protected $name;
    protected $id;
    protected $classes = array();
    protected $parent;
    protected $validators = [];
    protected $custom_validators = array();

    public function getValidators()
    {
        return $this->validators;
    }

    public function getCustomValidators()
    {
        return $this->custom_validators;
    }

    public function getId()
    {
        return $this->id;
    }

    // Parse validators into html format
    protected function parseValidators()
    {
        $html = "data-validators='";

        foreach ($this->validators as $validator) {
            $html .= "$validator ";
        }

        $html .= "'";

        return $html;
    }

    // Create a copy of the field as a confirmation (ex password -> confirm_password)
    public function getAsConfirmation(?string $label = null)
    {
        $copy = $this;
        $copy->id = $this->id . '-confirm';
        $copy->label_text = $label ? $label : $this->label_text . ' confirmation';
        $copy->validators = [];
        $copy->name = $this->name . '-confirm';
        return $copy->getView();
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function __construct($parent, $validators, $name, $label_text, $classes)
    {
        $this->parent = $parent;
        $this->classes = $classes;
        $this->classes[] = 'brickform-field';
        $this->name = $name;
        $this->label_text = $label_text;
        $this->validators = $validators;

        $this->parent->incrementItemCount();
    }

    public function addCustomValidator(?string $js_func_name)
    {
        $this->custom_validators[] = $js_func_name;
    }

    // return the widget as html format
    abstract public function getView();

    // return the widget html in a special div | labelized true if you want a label
    protected function inFormGroup(?string $view_html, ?bool $labelized = true)
    {
        $html = "<div class='brickform-form-group' id='brickform-group-" . $this->id . "'>\n\t";
        if ($labelized) {
            $html .= "<label for='$this->name'>" . $this->label_text . "</label>";
        }
        $html .= $view_html;
        $html .= "\n<ul class='brickform-errors'></ul></div>";

        return $html;
    }

    // return classes parsed as html
    protected function parseClasses($additionals = null)
    {
        $html = "class='";

        if ($additionals) {
            foreach ($additionals as $class)
                $html .= $class . " ";
        }

        foreach ($this->classes as $class) {
            $html .= $class . ' ';
        }
        $html .= "' ";
        return $html;
    }
}

class Field extends FormComponent
{

    // Override
    public function getView()
    {
        $html = "<input type='text' id='$this->id' name='$this->name' " . $this->parseClasses() . $this->parseValidators() . ">";
        return $this->inFormGroup($html);
    }

    public static function create($parent, $validators = [], $name = 'username', $label_text = 'Username', $classes = [])
    {
        $field = new Field($parent, $validators, $name, $label_text, $classes);

        $field->name = $field->name == null ? "username" : $field->name;
        $field->label_text = $field->label_text == null ? "Nom d'utilisateur" : $field->label_text;

        $field->id = 'brickform-field-' . $field->parent->getId() . '-' . $field->parent->getItemCount();
        return $field;
    }
}

class PasswordField extends FormComponent
{
    //Override
    public function getView()
    {
        $html = "<input type='password' id='$this->id' name='$this->name' " . $this->parseClasses() . $this->parseValidators() . ">";
        return $this->inFormGroup($html);
    }

    //Override
    public static function create($parent, $validators = [], $name = 'password', $label_text = 'Password', $classes = [])
    {
        $field = new PasswordField($parent, $validators, $name, $label_text, $classes);

        $field->name = $field->name == null ? "password" : $field->name;
        $field->label_text = $field->label_text == null ? "Mot de passe" : $field->label_text;

        $field->id = 'brickform-field-' . $field->parent->getId() . '-' . $field->parent->getItemCount();
        return $field;
    }
}

class NumberField extends FormComponent
{
    // Override
    public function getView()
    {
        $html = "<input type='number' id='$this->id' name='$this->name' ";

        foreach ($this->validators as $v) {
            $explode = explode(':', $v);
            if (count($explode) == 2) {
                if ($explode[0] == 'min')
                    $html .= "min=$explode[1] ";
                else if ($explode[0] == 'max')
                    $html .= "max=$explode[1] ";
            }
        }

        $html .= $this->parseClasses(['brickform-number']) . $this->parseValidators() . ">";
        return $this->inFormGroup($html);
    }

    public static function create($parent, $validators = [], $name = 'number', $label_text = 'Number', $classes = [])
    {
        $field = new NumberField($parent, $validators, $name, $label_text, $classes);

        $field->name = $field->name == null ? "number" : $field->name;
        $field->label_text = $field->label_text == null ? "Nombre" : $field->label_text;

        $field->id = 'brickform-field-' . $field->parent->getId() . '-' . $field->parent->getItemCount();
        return $field;
    }
}
