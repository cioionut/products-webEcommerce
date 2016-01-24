<?php

class Application_Form_SaveCurrency extends Zend_Form {

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

        // Add code field
        $input = new Zend_Form_Element_Text('code',array(
            'required'      => true,
            'label'         => 'Currency Code:',
            'id'            => 'currency_code',
            'placeholder'   => 'Example USD',
            'class'         => 'form-control',
            'list'          => 'currencies',
            'autocomplete'  => 'off',
        ));
        $validator = new Zend_Validate_StringLength(array('max' => 3));
        $input->addValidators(array($validator,new Zend_Validate_NotEmpty()));
        $currencyMapper = new Application_Model_CurrencyMapper();
        $decoratorCurrency = new My_Decorator_CurrencyAutocomplete(null, $currencyMapper->getAvailableCurrencies());
        $input->addDecorator($decoratorCurrency);
        $elements[] = $input;

        //add rate file
        $input = new Zend_Form_Element_Text('rate',array(
            'required'   => true,
            'label'      => 'Rate:',
            'id'         => 'rate',
            'placeholder'=> '...',
            'class'      => 'form-control',
            'step'       => 'any',
            'type'       => 'number',
        ));

        $input->addValidators(array(new Zend_Validate_Float(), new Zend_Validate_NotEmpty()));
        $input->addDecorator($decoratorField);
        $elements[] = $input;

        // Add checkbox field
        $input = new Zend_Form_Element_Checkbox('def',array(
            'label'      => 'Default',
            'id'         => 'def',
            'class'      => 'checkbox',
            'type'       => 'checkbox',
        ));
        $decoratorCheckBox = new My_Decorator_CheckBox();
        $input->addDecorator($decoratorCheckBox);
        $elements[] = $input;

        // Add checkbox field
        $input = new Zend_Form_Element_Checkbox('active',array(
            'label'      => 'Active',
            'id'         => 'active',
            'class'      => 'checkbox',
            'type'       => 'checkbox',
        ));
        $decoratorCheckBox = new My_Decorator_CheckBox();
        $input->addDecorator($decoratorCheckBox);
        $elements[] = $input;


        //Add Submit button
        $input = new Zend_Form_Element_Submit('submit',array(
            'Label'      => '',
            'class'      => 'btn btn-default',
            'value'      => 'Save',
        ));
        $input->addDecorator($decoratorField);
        $elements[] = $input;


        $this->addElements($elements);

        $this->addDisplayGroup(
            array('code', 'rate', 'def', 'active', 'submit'),
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

