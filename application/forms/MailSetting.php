<?php

class Application_Form_MailSetting extends Zend_Form {


    public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->setAttribs(
            array(
                'class' => 'form-horizontal' ,
            )
        );

        $decoratorField = new My_Decorator_Field();

        $elements = array();

        // Add name field
        $input = new Zend_Form_Element_Text('host',array(
            'required'   => true,
            'label'      => 'SMTP Host:',
            'id'         => 'host',
            'placeholder'=> 'Type something..',
            'class'      => 'form-control',
            'value'     => 'smtp.gmail.com',
        ));

        $input->addValidators(array(new Zend_Validate_NotEmpty()));
        $input->addDecorator($decoratorField);
        $elements[] = $input;

        // Add category field
        $select = new Zend_Form_Element_Select('stype',array(
            'required'   => true,
            'label'      => 'Security:',
            'id'         => 'stype',
            'class'      => 'form-control',
        ));

        $select->addMultiOption('TLS', 'TLS');
        $select->addMultiOption('SSH', 'SSH');
        $select->setValue('TLS');

        $select->addDecorator($decoratorField);
        $elements[] = $select;

        // Add Price field
        $input = new Zend_Form_Element_Text('port',array(
            'required'   => true,
            'label'      => 'Port:',
            'id'         => 'port',
            'placeholder'=> 'Type something..',
            'class'      => 'form-control',
            'min'        =>  0,
            'step'       => '1',
            'type'       => 'number',
            'value'     => '587',
        ));

        $min = new Zend_Validate_GreaterThan(0);

        $input->addValidators(array(new Zend_Validate_Digits(), $min, new Zend_Validate_NotEmpty()));
        $input->addDecorator($decoratorField);
        $elements[] = $input;

        $input = new Zend_Form_Element_Text('email',array(
            'required'   => true,
            'label'      => 'SMTP Email Address:',
            'id'         => 'email',
            'placeholder'=> 'Your email..',
            'class'      => 'form-control',
            'type'       => 'email',
            'value'      => 'testarnia@gmail.com',
        ));

        $input->addValidators(array(new Zend_Validate_EmailAddress(),new Zend_Validate_NotEmpty()));
        $input->addDecorator($decoratorField);
        $elements[] = $input;

        // Add category field
        $input = new Zend_Form_Element_Password('password1',array(
            'required'   => true,
            'label'      => 'Password:',
            'id'         => 'password1',
            'class'      => 'form-control',
            'placeholder'=> 'Your SMTP password..',
        ));

        $input->addValidators(array(new Zend_Validate_NotEmpty()));

        $input->addDecorator($decoratorField);
        $elements[] = $input;

        // Add category field
        $input = new Zend_Form_Element_Password('password2',array(
            'required'   => true,
            'label'      => 'Password Again:',
            'id'         => 'password2',
            'class'      => 'form-control',
            'placeholder'=> 'Your SMTP password again..',
            'validators' => array(
                array('identical', false, array('token' => 'password1')))
        ));

        $input->addDecorator($decoratorField);
        $elements[] = $input;


        //Add Submit button

        $input = new Zend_Form_Element_Submit('submit',array(
            'Label'      => ' ',
            'class'      => 'btn btn-info',
            'value'      => 'Add New Configuration',
        ));


        $input->addDecorator($decoratorField);
        $elements[] = $input;


        $this->addElements($elements);

        $this->addDisplayGroup(
            array('host', 'stype' ,'port', 'email', 'password1' ,'password2','submit'),
            'displgrp',
            array(
                'legend' => 'Add Products',
                'decorators' => array(
                    'FormElements',
                    'Fieldset',
                    /*// need to alias the HtmlTag decorator so you can use it twice
                    array(array('Dashed'=>'HtmlTag'), array('tag'=>'div', 'class'=>'dashed-outline')),
                    array('HtmlTag',array('tag' => 'div',  'class' => 'settings')),*/
                )
            )
        );

        return $this;
    }

}