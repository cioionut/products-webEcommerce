<?php

class CurrenciesController  extends Zend_Controller_Action
{

    const VALIDATE_FORM = 'validateForm';

    public function indexAction()
    {
        $this->redirect('save');
    }

    public function saveAction()
    {
        $id = $this->getParam('id');
        $form = new Application_Form_SaveCurrency();
        if ($id) {
            $currencyMapper = new Application_Model_CurrencyMapper();
            $currency = $currencyMapper->find($id);
            $form->getElement('code')->setValue($currency->code);
            $form->getElement('rate')->setValue(number_format((float)$currency->rate,4));
            $form->getElement('def')->setValue($currency->def);
            $form->getElement('active')->setValue($currency->active);


            if (!$currency->def) {
                $delForm = new Application_Form_SubmitButton();
                $delForm->setAction($this->view->url(array('controller' => 'currencies', 'action' => 'delete'), null, true));
                $delForm->addAttribs(array(
                    'id' => 'delSettingForm' . $id,
                    'onsubmit' => self::VALIDATE_FORM . "('delSettingForm" . $id . "')",
                    'class' => 'form-horizontal'
                ));
                $delForm->getElement('id')->setValue($id);
                $delForm->getElement('submit')->setAttribs(array(
                    'class' => 'btn btn-danger',
                ));
                $delForm->getElement('submit')->setLabel('Delete');
                $this->view->delForm = $delForm;
            }
        }
        $form->getElement('active')->setValue(1);
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $currency = new Application_Model_Currency($data);
                $currency->setId($id);
                $currencyMapper = new Application_Model_CurrencyMapper();
                //var_dump($data, $currency);
                try {
                    $currencyMapper->save($currency);
                    if($currency->def) $currencyMapper->updater();
                } catch (Exception $e) {
                    $this->_helper->getHelper('FlashMessenger')->addMessage($e->getMessage(), 'error');
                }

                return $this->_helper->redirector('dashboard', 'users');
            }
        } else {
            foreach ($form->getMessages() as $error) {
                $this->_helper->getHelper('FlashMessenger')->addMessage(array_shift(array_values($error)), 'error');
                $this->_helper->redirector('save');
                //var_dump(array_shift(array_values($error)));
            }
        }
        $this->view->form = $form;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_SubmitButton();

        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $currencyMapper = new Application_Model_CurrencyMapper();
                if (isset($data['id'])) $currencyMapper->getDbTable()->delete(array('id = ?' => $data['id'], 'def <> ?' => 1));
                return $this->_helper->redirector('dashboard', 'users');
            }
        }
    }

    public function updaterAction()
    {
        $currencyMapper = new Application_Model_CurrencyMapper();
        $currencyMapper->updater();
        //var_dump($currencies);
        $this->_helper->redirector('dashboard', 'users');
    }
}

