<?php

class Application_Form_DeleteProduct extends Zend_Form {
    const MIN = 0;
    const MAX = 5000;

    public function init(){
        $this->setMethod('post');
        $decoratorField = new My_Decorator_Field();
        $elements = array();
        //Add id hidden field

        $input = new Zend_Form_Element_Hidden('product_id');

        $min = new Zend_Validate_GreaterThan(self::MIN);

        $input->addValidators(array(new Zend_Validate_Digits(), $min, new Zend_Validate_NotEmpty()));
        $elements[] = $input;


        //Add Submit button
        $input = new Zend_Form_Element_Submit('submit',array(
            'Label'      => '',
            'class'      => 'btn btn-danger',
            'value'      => 'Delete',
        ));
        $elements[] = $input;
        $input->addDecorator($decoratorField);
        $this->addElements($elements);

    }
}