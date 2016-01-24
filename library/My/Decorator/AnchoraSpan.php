<?php

class My_Decorator_AnchoraSpan extends Zend_Form_Decorator_Abstract
{

    protected $_format;

    public function __construct($options = NULL){

        parent:: __construct($options);
        $this->_format = "
        <a href = '%s'>
            <span class = '%s' style = '%s'></span>
        </a>";
    }



    public function render($content)
    {
        $element = $this->getElement();

        $attribs = $element->getAttribs();


        $markup  = sprintf($this->_format, $attribs['href'], $attribs['spanClass'], $attribs['spanStyle']);

        return $markup;
    }
}