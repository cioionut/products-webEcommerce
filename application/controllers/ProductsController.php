<?php

class ProductsController extends Zend_Controller_Action {
    const MIN = 0;
    const MAX = 5000;
    const VALIDATE_FORM = 'validateForm';
    const DELETE_FIELD = 'delete_field';
    const COUNT_CART = 'countCart';
    const ADD_TO_CART = 'addToCart';

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        return $this->_helper->redirector('shop');
    }

    public function shopAction(){
        $category_id = $this->getParam('category');
        $productMapper = new Application_Model_ProductMapper();
        $categoriesMapper = new Application_Model_CategoryMapper();
        $this->view->headScript()->appendFile(JS_DIR . '/' . self::VALIDATE_FORM . '.js');
        $this->view->headScript()->appendFile(JS_DIR . '/' . self::COUNT_CART . '.js');
        $this->view->headScript()->appendFile(JS_DIR . '/' . self::ADD_TO_CART . '.js');

        $auth = Zend_Auth::getInstance();
        $currentUser = null;
        if($auth->hasIdentity()) $currentUser = $auth->getIdentity();
        $this->view->user_id = ($currentUser) ? $currentUser->id : null;
        $currency_id = ($currentUser) ? $currentUser->currency_id : null;
        $this->view->categories = $categoriesMapper->fetchAll();

        //var_dump($auth->getIdentity());

        $this->view->products = $productMapper->fetchAll($currency_id, $category_id);

        /*if($category_id){
            $this->view->products = $productMapper->getDbTable()->fetchAll($productMapper->getDbTable()->select()->where('category_id = ?', $category_id));
        }
        else {
            $this->view->products = $productMapper->fetchAll(true,null);
        }*/

        $this->view->newproducts = $productMapper->getDbTable()->fetchAll($productMapper->getDbTable()->select()->order('id DESC')->limit('3'));


        $this->_helper->layout->setLayout('shop');
    }

    public function viewallAction() {
        $productMapper = new Application_Model_ProductMapper();

        $this->view->headScript()->appendFile(JS_DIR . '/' . self::VALIDATE_FORM . '.js');

        $this->view->products = $productMapper->fetchAll();

        $forms = array();

        foreach($this->view->products as $i => $product){

            $delForm = new Application_Form_DeleteProduct();
            $delForm->setAction( $this->view->url(array('controller' => 'products','action' => 'delete'), null, true));
            $delForm->addAttribs(array(
                'id' => 'delForm' . $product->id,
                'onsubmit' => self::VALIDATE_FORM . "('delForm" . $product->id . "')",
            ));
            $delForm->getElement('product_id')->setValue($product->id);
            $forms['delProductForm'][] = $delForm;
        }
        $this->view->forms = $forms;
    }

    public function viewAction(){
        $this->view->headScript()->appendFile(JS_DIR . '/' . self::COUNT_CART . '.js');
        $id = $this->getParam('id');
        if($id){

            $auth = Zend_Auth::getInstance();
            $currentUser = null;
            if($auth->hasIdentity()) $currentUser = $auth->getIdentity();
            $currency_id = ($currentUser) ? $currentUser->currency_id : null;

            $productMapper = new Application_Model_ProductMapper();
            $categoriesMapper = new Application_Model_CategoryMapper();
            $this->view->categories = $categoriesMapper->fetchAll();
            $this->view->product = $productMapper->getProductById($id, $currency_id);
            $this->_helper->layout->setLayout('shop');
        }
        else {
            return $this->_helper->redirector('shop');
        }
    }

    public function deleteAction(){
        $request = $this->getRequest();
        $form = new Application_Form_DeleteProduct();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $productMapper = new Application_Model_ProductMapper();
                if(isset($data['product_id']))  $productMapper->delete($data['product_id']);
                return $this->_helper->redirector('viewall');
            }
        }
    }

    public function saveAction(){
        $request = $this->getRequest();
        $id = $this->getParam('id');
        $auth = Zend_Auth::getInstance();

        if($auth->hasIdentity()) {
            $user_id = $auth->getIdentity()->id;
        }

        $form = $this->getSaveProductForm($id);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {

                $data = $this->getRequest()->getParams();

                $upload = new Zend_File_Transfer();

                $files = $upload->getFileInfo();
                $isValid = true;

                foreach ($files as $field => $file)
                {
                    if(!strlen($file["name"]))
                    {
                        continue;
                    }

                    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = pathinfo($file['name'], PATHINFO_FILENAME);

                    if(!file_exists(UPLOADS_IMAGES)) mkdir(UPLOADS_IMAGES, 0774, true);
                    if(!file_exists(UPLOADS_DATA)) mkdir(UPLOADS_DATA, 0774, true);

                    // upload instructions for image
                    if ($field == 'image')
                    {
                        $upload->addFilter('Rename', array('target' => UPLOADS_IMAGES . '/' . $filename . "_" . $user_id . "_" . time() . "." . $extension, 'overwrite' => TRUE), $field)
                               ->addValidator('Extension', false, array('jpg', 'jpeg', 'png'), $field);
                        $data['image'] = $filename . "_" . $user_id . "_" . time() . "." . $extension;
                    }

                    // upload instructions for file
                    if ($field == 'file')
                    {
                        $upload->addFilter('Rename', array('target' => UPLOADS_DATA . '/' . $filename . "_" . $user_id . "_" . time() . "." . $extension, 'overwrite' => TRUE), $field)
                               ->addValidator('Extension', false, array('doc', 'docx', 'txt', 'pdf'), $field);
                        $data['file'] = $filename . "_" . $user_id . "_" . time() . "." . $extension;
                    }

                    if($upload->isValid($field)) {
                        if (!$upload->receive($field)) {
                            $isValid = false;
                            foreach ($upload->getMessages() as $key => $val) {
                                $this->_helper->getHelper('FlashMessenger')->addMessage($val, 'error');
                            }
                        }
                    }
                    else{
                        $isValid = false;
                        $this->_helper->getHelper('FlashMessenger')->addMessage($file['name'] . " is not valid $field", 'error');
                        //return $this->_helper->redirector('save');
                    }
                }

                if ($upload->hasErrors()) {
                    $errors = $upload->getMessages();
                    foreach ($errors as $error){
                        $this->_helper->getHelper('FlashMessenger')->addMessage("$error", 'error');
                    }
                    return $this->_helper->redirector('save');
                }

                if($isValid){
                    $product = new Application_Model_Product();
                    $productMapper = new Application_Model_ProductMapper();
                    if($id) {
                        $product = $productMapper->getProductById($id);
                    }

                    if((isset($data['file']) && $product->file && $product->file != $data['file']) || (!isset($data['file']) && $product->file)){
                        $productMapper->delete_file($product->file);
                    }

                    if((isset($data['image']) && $product->image && $product->image != $data['image']) || (!isset($data['image']) && $product->image)){
                        $productMapper->delete_image($product->image);
                    }

                    $product = new Application_Model_Product($data);

                    $productMapper->save($product);
                    return $this->_helper->redirector('dashboard', 'users');
                }
            }
        }

        $this->view->headScript()->appendFile(JS_DIR . '/' . self::DELETE_FIELD . '.js');

        $this->view->form = $form;
    }

    public function getSaveProductForm($id)
    {

        $form = new Zend_Form();

        //get product whitch want update
        $productMapper = new Application_Model_ProductMapper();
        $product = new Application_Model_Product();

        if ( $id ) $product = $productMapper->getProductById($id);

        // Set the method for the display form to POST
        $form->setMethod('post');
        $form->setAttribs(
            array(
                'class' => 'form-horizontal' ,
                'enctype' => 'multipart/form-data' ,
            )
        );

        $decoratorField = new My_Decorator_Field();

        $elements = array();
        //Add id hidden field
        $input = new Zend_Form_Element_Hidden('id',array('value' => $id));
        $elements[] = $input;

        // Add name field
        $input = new Zend_Form_Element_Text('name',array(
            'required'   => true,
            'label'      => 'Name:',
            'id'         => 'name',
            'placeholder'=> 'Type something..',
            'value'      =>  $product->getName(),
            'class'      => 'form-control',
        ));

        $input->addValidators(array(new Zend_Validate_Alnum(),new Zend_Validate_NotEmpty()));
        $input->addDecorator($decoratorField);
        $elements[] = $input;

        // Add category field
        $select = new Zend_Form_Element_Select('category_id',array(
            'required'   => true,
            'label'      => 'Category:',
            'id'         => 'category',
            'class'      => 'form-control',
        ));

        $categoryMapper = new Application_Model_CategoryMapper();
        $categories = $categoryMapper->fetchAll();
        foreach($categories as $category){
            $select->addMultiOption($category->getId(), $category->getName());
        }
        // set selected option
        $select->setValue($product->getCategoryId());

        $select->addDecorator($decoratorField);
        $elements[] = $select;
        $currencyMapper = new Application_Model_CurrencyMapper();
        $currency = $currencyMapper->getDefaultCurrency();
        // Add Price field
        $input = new Zend_Form_Element_Text('price',array(
            'required'   => true,
            'label'      => 'Price in ' . $currency->getCode() .':',
            'id'         => 'price',
            'placeholder'=> 'Type something..',
            'value'      =>  number_format((float)$product->price, 2),
            'class'      => 'form-control',
            'min'        => self :: MIN,
            'max'        => self :: MAX,
            'step'       => 'any',
            'type'       => 'number',
        ));

        $min = new Zend_Validate_LessThan(self::MAX);
        $max = new Zend_Validate_GreaterThan(self::MIN);

        $input->addValidators(array(new Zend_Validate_Float(), $min, $max, new Zend_Validate_NotEmpty()));
        $input->addDecorator($decoratorField);
        $elements[] = $input;



        if($id) {
            //Add File field
            if($product->file) {
                $input = new Zend_Form_Element('file', array(
                    'label' => 'File:',
                    'id' => 'file',
                    'class' => 'form-control',
                    'value' => $product->file,
                ));
                $input->addDecorator(new My_Decorator_AnchoraFileForm());
                $elements[] = $input;
            }
            else {
                $input = new Zend_Form_Element_File('file', array(
                    'label' => 'File:',
                    'id' => 'file',
                    'class' => 'form-control',
                ));
                $input->addDecorator($decoratorField);
                $elements[] = $input;
            }

            //Add Image field
            if($product->image) {
                $input = new Zend_Form_Element('image', array(
                    'label' => 'Image:',
                    'id' => 'image',
                    'class' => 'form-control',
                    'value' => $product->image,
                ));
                $input->addDecorator(new My_Decorator_ImageForm());
                $elements[] = $input;
            }
            else {
                $input = new Zend_Form_Element_File('image', array(
                    'label' => 'Image:',
                    'id' => 'image',
                    'class' => 'form-control',
                ));
                $input->addDecorator($decoratorField);
                $elements[] = $input;
            }

        } else {
            //Add File field
            $input = new Zend_Form_Element_File('file', array(
                'label' => 'File:',
                'id' => 'file',
                'class' => 'form-control',
            ));

            $input->addDecorator($decoratorField);
            $elements[] = $input;

            //Add Image field
            $input = new Zend_Form_Element_File('image', array(
                'label' => 'Image:',
                'id' => 'image',
                'class' => 'form-control',
            ));

            $input->addDecorator($decoratorField);
            $elements[] = $input;

        }

        //Add Description field
        $input = new Zend_Form_Element_Textarea('description', array(
            'label' => 'Description:',
            'id' => 'description',
            'class' => 'form-control',
            'value' => $product->description,
        ));


        $input->addDecorator($decoratorField);
        $elements[] = $input;

        //Add Submit button
        if(!$id) {
            $input = new Zend_Form_Element_Submit('submit',array(
                'Label'      => ' ',
                'class'      => 'btn btn-success',
                'value'      => 'Add New Product',
            ));
        }
        else {
            $input = new Zend_Form_Element_Submit('submit',array(
                'Label'      => ' ',
                'class'      => 'btn btn-info',
                'value'      => 'Update Product',
            ));
        }

        $input->addDecorator($decoratorField);
        $elements[] = $input;


        $form->addElements($elements);

        $form->addDisplayGroup(
            array('name', 'category_id' ,'price', 'currency_id', 'file', 'image' ,'description','submit'),
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

        return $form;
    }


}

