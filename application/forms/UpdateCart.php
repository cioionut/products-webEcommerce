<?php

class Application_Form_UpdateCart extends Zend_Form {
    const MIN = 0;
    const MAX = 100;

    public function init(){
        $this->setMethod('post');
        $this->setAttrib('class','col-md-9');
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

        // Add Quantity field
        $input = new Zend_Form_Element_Text('quantity',array(
            'required'   => true,
            'class'      => '"col-md-6"',
            'min'        => self :: MIN,
            'max'        => self :: MAX,
            'step'       => '1',
            'type'       => 'number',
        ));

        $min = new Zend_Validate_LessThan(self::MAX + 1);
        $max = new Zend_Validate_GreaterThan(self::MIN);

        $input->addValidators(array(new Zend_Validate_Float(), $min, $max, new Zend_Validate_NotEmpty()));
        $input->addDecorator($decoratorField);
        $elements[] = $input;

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