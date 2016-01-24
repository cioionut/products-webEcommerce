<?php

class Application_Form_ChPass extends Zend_Form {

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

        // Add oldpass field
        $input = new Zend_Form_Element_Password('oldpassword',array(
            'required'   => true,
            'label'      => 'Old Password:',
            'id'         => 'oldpassword',
            'placeholder'=> 'Old pass..',
            'class'      => 'form-control',
        ));

        $input->addValidator(new Zend_Validate_NotEmpty());
        $input->addDecorator($decoratorField);
        $elements[] = $input;

        // Add newpass1 field
        $input = new Zend_Form_Element_Password('newpassword1',array(
            'required'   => true,
            'label'      => 'New Password:',
            'id'         => 'newpassword1',
            'class'      => 'form-control',
            'placeholder'=> 'Your password..',
        ));

        $input->addValidators(array(new Zend_Validate_Alnum(), new Zend_Validate_StringLength(array('min' => 8)), new Zend_Validate_NotEmpty()));

        $input->addDecorator($decoratorField);
        $elements[] = $input;

        // Add newpass2 field
        $input = new Zend_Form_Element_Password('newpassword2',array(
            'required'   => true,
            'label'      => 'New Password Again:',
            'id'         => 'newpassword2',
            'class'      => 'form-control',
            'placeholder'=> 'Your password again..',
            'validators' => array(
                array('identical', false, array('token' => 'newpassword1')))
        ));

        $input->addDecorator($decoratorField);
        $elements[] = $input;


        //Add Submit button
        $input = new Zend_Form_Element_Submit('submit',array(
            'Label'      => '',
            'class'      => 'btn btn-default',
            'value'      => 'Update',
        ));
        $input->addDecorator($decoratorField);
        $elements[] = $input;


        $this->addElements($elements);

        $this->addDisplayGroup(
            array('oldpassword', 'newpassword1' , 'newpassword2', 'submit'),
            'displgrp',
            array(
                'decorators' => array(
                    'FormElements',
                    /*// need to alias the HtmlTag decorator so you can use it twice
                    array(array('Dashed'=>'HtmlTag'), array('tag'=>'div', 'class'=>'dashed-outline')),
                    array('HtmlTag',array('tag' => 'div',  'class' => 'settings')),*/
                )
            )
        );
    }
}