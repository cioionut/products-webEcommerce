<?php

class MailsettingsController extends Zend_Controller_Action{

    public function indexAction(){
        $mailMapper = new Application_Model_MailsettingMapper();
        $mailSetting = $mailMapper->fetchAll();
        var_dump($mailSetting);
    }

    public function saveAction(){
        $id = $this->getParam('id');
        $mailMapper = new Application_Model_MailsettingMapper();
        $form = new Application_Form_MailSetting();
        if ($id) {
            $json_config = $mailMapper->getConfig($id);
            $cript_obj = new My_Class_Cript();
            $form->getElement('host')->setValue($json_config->host);
            $form->getElement('port')->setValue($json_config->port);
            $form->getElement('stype')->setValue($json_config->stype);
            $form->getElement('email')->setValue($json_config->email);
            $form->getElement('email')->setValue($json_config->email);
            $form->getElement('submit')->setValue('Update Configuration');
        }
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $obj = new My_Class_Cript();
                $data['password'] = $obj->cript($data['password1']);
                unset($data['password1']);
                unset($data['password2']);
                $json = json_encode($data);
                $data = null;
                $data['id'] = $id;
                $data['json_config'] = $json;
                $mailSetting = new Application_Model_Mailsetting($data);
                $mailMapper->save($mailSetting);

                $this->_helper->redirector('dashboard', 'users');
            }
            else {
                foreach ($form->getMessages() as $error){
                    $this->_helper->getHelper('FlashMessenger')->addMessage(array_shift(array_values($error)), 'error');
                    $this->_helper->redirector('save');
                }
            }
        }
        $this->view->form = $form;
    }

    public function deleteAction(){
        $request = $this->getRequest();
        $form = new Application_Form_SubmitButton();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $mailMapper = new Application_Model_MailsettingMapper();
                if(isset($data['id']))  $mailMapper->delete($data['id']);
                return $this->_helper->redirector('dashboard', 'users');
            }
        }
    }

    public function mkdefaultAction(){
        $request = $this->getRequest();
        $form = new Application_Form_SubmitButton();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $mailMapper = new Application_Model_MailsettingMapper();
                if(isset($data['id']))  $mailMapper->setDefault($data['id']);
                return $this->_helper->redirector('dashboard', 'users');
            }
        }
    }
}