<?php

class Application_Form_Login extends Zend_Form {

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
        $validator->setOptions(array('domain' => false));

        $input->addValidators(array($validator,new Zend_Validate_NotEmpty()));
        $input->addDecorator($decoratorField);
        $elements[] = $input;

        // Add password field
        $input = new Zend_Form_Element_Password('password',array(
            'required'   => true,
            'label'      => 'Password:',
            'id'         => 'password',
            'class'      => 'form-control',
            'placeholder'=> 'Your password..',
        ));

        $input->addValidators(array(new Zend_Validate_NotEmpty()));
        $input->addDecorator($decoratorField);
        $elements[] = $input;

        // Add checkbox field
        $input = new Zend_Form_Element_Checkbox('rememberMe',array(
            'label'      => 'Remember me',
            'id'         => 'rememberMe',
            'class'      => 'checkbox',
            'type'       => 'checkbox',
        ));
        $decoratorCheckBox = new My_Decorator_CheckBox();
        $input->addDecorator($decoratorCheckBox);
        $elements[] = $input;

        $input = new Zend_Form_Element('resetpass', array(
            'label' => 'Reset your password',
            'id' => 'resetpass',
            'class' => 'form-control',
            'value' => 'resetpass',
        ));
        $input->addDecorator(new My_Decorator_AnchoraForm());
        $elements[] = $input;

        //Add Submit button
        $input = new Zend_Form_Element_Submit('submit',array(
                'Label'      => '',
                'class'      => 'btn btn-default',
                'value'      => 'Login',
            ));
        $input->addDecorator($decoratorField);
        $elements[] = $input;


        $this->addElements($elements);

        $this->addDisplayGroup(
            array('email', 'password', 'resetpass', 'rememberMe', 'submit'),
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