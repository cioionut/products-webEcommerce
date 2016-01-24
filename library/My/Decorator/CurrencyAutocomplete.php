<?php

class My_Decorator_CurrencyAutocomplete extends Zend_Form_Decorator_Abstract
{
    /**#@+
     * Constants that are used for types of elements
     *
     * @var string
     */
    const DEFAULT_TYPE = 'text';
    const FIELD_EMAIL = 'email';
    const FIELD_EMAIL_ADDRESS = 'emailaddress';
    const FIELD_URL = 'url';
    const FIELD_NUMBER = 'number';
    const FIELD_RANGE = 'range';
    const FIELD_DATE = 'date';
    const FIELD_MONTH = 'month';
    const FIELD_WEEK = 'week';
    const FIELD_TIME = 'time';
    const FIELD_DATE_TIME = 'datetime';
    const FIELD_DATE_TIME_LOCAL = 'datetime-local';
    const FIELD_SEARCH = 'search';
    const FIELD_COLOR = 'color';
    /**#@-*/

    /**
     * Mapping of key => value pairs for the elements
     *
     * @var array
     */
    protected static $_mapping = array(
        self::FIELD_EMAIL => 'email',
        self::FIELD_EMAIL_ADDRESS => 'email',
        self::FIELD_URL => 'url',
        self::FIELD_NUMBER => 'number',
        self::FIELD_RANGE => 'range',
        self::FIELD_DATE => 'date',
        self::FIELD_MONTH => 'month',
        self::FIELD_WEEK => 'week',
        self::FIELD_TIME => 'time',
        self::FIELD_DATE_TIME => 'datetime',
        self::FIELD_DATE_TIME_LOCAL => 'datetime-local',
        self::FIELD_SEARCH => 'search',
        self::FIELD_COLOR => 'color',
    );
    protected $_format;

    public function __construct($options = null, $currencies = null) {
        parent::__construct($options);
        $this->_format = "<div class='form-group'>
            <label class = 'control-label col-sm-1' for = '%s' id = '%s'> %s </label>
            <div class='col-sm-2' id ='%s'>
            %s ";
        $this->_format .= "<datalist id = 'currencies' >";
        foreach( $currencies as $currency ) {
            $code = $currency->code;
            $this->_format .= "<option value = '$code' >";
        }
        $this->_format .= "</datalist></div></div>";
    }

    public function buildInput()
    {

        $element = $this->getElement();
        $helper  = $element->helper;
        return $element->getView()->$helper(
            $element->getName(),
            $element->getValue(),
            $element->getAttribs(),
            $element->options
        );
    }

    public function render($content)
    {
        $element = $this->getElement();

        //var_dump($element);

        $name    = htmlentities($element->getFullyQualifiedName());
        $label   = htmlentities($element->getLabel());
        $id      = htmlentities($element->getId());

        $input = $this->buildInput();

        if($element->getType() == 'Zend_Form_Element_Text' && isset($element->type) && array_key_exists(strtolower($element->type), self::$_mapping))  {

            $n1 = strpos($input,'"');
            $n2 = strpos($input,'"',$n1+1);

            $s1 = substr($input, 0 ,$n1+1);
            $s2 = substr($input, $n2, strlen($input));

            $newInput = $s1 . $element->type . $s2;
            $input = $newInput;
        }

        /*if ($element->getDecorator('My_Decorator_InputCostumType')) $input = $element->getDecorator('My_Decorator_InputCostumType')->render('test');
        else $input = $this->buildInput();*/

        $markup  = sprintf($this->_format, $name, $id, $label, $id, $input);

        return $markup;
    }
}