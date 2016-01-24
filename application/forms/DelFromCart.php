<?php

class Application_Form_DelFromCart extends Zend_Form {
    const MIN = 0;
    const MAX = 100;

    public function init(){
        $this->setMethod('post');

        $decoratorField = new My_Decorator_Field();
        $elements = array();
        //render our form elements and the "form" tag
        $this->setDecorators(
            array(
                'FormElements',
                'Form'
            )
        );
        $this->setElementDecorators(
            array(
                'ViewHelper',
                'Label'
            )
        );

        //Add id hidden field
        $input = new Zend_Form_Element_Hidden('product_id');

        $min = new Zend_Validate_GreaterThan(self::MIN);

        $input->addValidators(array(new Zend_Validate_Digits(), $min, new Zend_Validate_NotEmpty()));
        $input->removeDecorator('HtmlTag');
        $input->removeDecorator('Label');

        $elements[] = $input;

        $this->addElements($elements);


    }
}