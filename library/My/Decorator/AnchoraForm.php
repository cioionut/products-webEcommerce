<?php

class My_Decorator_AnchoraForm extends Zend_Form_Decorator_Abstract
{

    protected $_format;

    public function __construct($options = NULL){

        parent:: __construct($options);
        $this->_format = "
        <div class='form-group'>
            <label class = 'control-label col-sm-1' for = '%s'>%s</label>
            <div class='col-sm-2' id ='%s'>
                <a href='%s' > %s </a>
            </div>
         </div>";
    }



    public function render($content)
    {
        $element = $this->getElement();

        //var_dump($element);

        $name    = htmlentities($element->getFullyQualifiedName());
        $label   = htmlentities($element->getLabel());
        $id      = htmlentities($element->getId());
        $value   = htmlentities($element->getValue());

        $markup  = sprintf($this->_format, $name, '', $id, $value, $label);

        return $markup;
    }
}