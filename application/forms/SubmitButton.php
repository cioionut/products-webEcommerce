<?php

class Application_Form_SubmitButton extends Zend_Form {

    public function init(){
        $this->setMethod('post');

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
        $input = new Zend_Form_Element_Hidden('id');
        $input->addValidators(array(new Zend_Validate_Digits(), new Zend_Validate_NotEmpty()));
        $input->removeDecorator('HtmlTag');
        $input->removeDecorator('Label');
        $elements[] = $input;

        //Add submit button
        $input = new Zend_Form_Element_Submit('submit');
        $input->removeDecorator('HtmlTag');
        $input->removeDecorator('Label');
        $input->removeDecorator('DtDdWrapper');
        $elements[] = $input;

        $this->addElements($elements);
    }
}