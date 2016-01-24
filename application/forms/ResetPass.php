<?php

class Application_Form_ResetPass extends Zend_Form {

    public function init(){
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttribs(
            array(
                'class' => 'form-horizontal' ,
            )
        );

        $decoratorField = new My_Decorator_FieldLogin();

        $elements = array();

        // Add email field
        $input = new Zend_Form_Element_Text('email',array(
            'required'   => true,
            'label'      => 'Email Address:',
            'id'         => 'email',
            'placeholder'=> 'Your email..',
            'class'      => 'form-control',
            'type'       => 'email',
        ));

        $validator = new Zend_Validate_EmailAddress();

        $input->addValidators(array($validator,new Zend_Validate_NotEmpty()));
        $input->addDecorator($decoratorField);
        $elements[] = $input;


        //Add Submit button
        $input = new Zend_Form_Element_Submit('submit',array(
            'Label'      => '',
            'class'      => 'btn btn-default',
            'value'      => 'Reset',
        ));
        $input->addDecorator($decoratorField);
        $elements[] = $input;


        $this->addElements($elements);

        $this->addDisplayGroup(
            array('email', 'submit'),
            'displgrp',
            array(
                'decorators' => array(
                    'FormElements',
                    'Fieldset',
                    /*// need to alias the HtmlTag decorator so you can use it twice
                    array(array('Dashed'=>'HtmlTag'), array('tag'=>'div', 'class'=>'dashed-outline')),
                    array('HtmlTag',array('tag' => 'div',  'class' => 'settings')),*/
                )
            )
        );
    }
}