<?php

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;
use PayPal\Rest\ApiContext;
use PayPal\Api\Sale;


class UsersController extends Zend_Controller_Action {

    const MIN = 0;
    const MAX = 5000;
    const DELETE_FROM_CART = 'delFromCart';
    const VALIDATE_FORM = 'validateForm';
    const COUNT_CART = 'countCart';
    const ADD_TO_CART = 'addToCart';
    const PROCESS_PAYMENT = 'processPayment';
    const STATE_UPDATE = 'stupdate';


    public function indexAction() {
        return $this->_helper->redirector('login', 'auth');
    }

    public function dashboardAction() {

        $this->view->headScript()->appendFile(JS_DIR . '/' . self::VALIDATE_FORM . '.js');
        $this->view->headScript()->appendFile(JS_DIR . '/' . self::STATE_UPDATE . '.js');
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $currentUser = $auth->getIdentity();
        }

        $productMapper = new Application_Model_ProductMapper();
        $this->view->products = $productMapper->fetchAll();

        $userMapper = new Application_Model_UserMapper();
        $this->view->users = $userMapper->fetchAll();

        $mailMapper = new Application_Model_MailsettingMapper();
        $this->view->mailSettings = $mailMapper->fetchAll();

        $orderMapper = new Application_Model_OrderMapper();
        $this->view->orders = $orderMapper->fetchAll();

        $currencyMapper = new Application_Model_CurrencyMapper();
        $this->view->currencies = $currencyMapper->fetchAll();

        $forms = array();
        foreach ($this->view->mailSettings as $setting) {
            $form = new Application_Form_SubmitButton();
            $form->setAction( $this->view->url(array('controller' => 'mailsettings','action' => 'delete'), null, true));
            $form->addAttribs(array(
                'id' => 'delSettingForm' . $setting->id,
                'onsubmit' => self::VALIDATE_FORM . "('delSettingForm" . $setting->id . "')",
            ));
            $form->getElement('id')->setValue($setting->id);
            $form->getElement('submit')->setAttribs(array (
                'class' => 'btn btn-danger',
            ));
            $form->getElement('submit')->setLabel('Delete');
            $forms['delSettingForm'][] = $form;

            $form = new Application_Form_SubmitButton();

            if($setting->getDefaultConfig()) {
                $form->getElement('submit')->setAttribs(array (
                    'class' => 'btn btn-primary disabled',
                ));
                $form->getElement('submit')->setLabel('Default');
            } else {
                $form->addAttribs(array(
                    'id' => 'defSettingForm' . $setting->id,
                    'onsubmit' => self::VALIDATE_FORM . "('defSettingForm" . $setting->id . "')",
                ));
                $form->setAction( $this->view->url(array('controller' => 'mailsettings','action' => 'mkdefault'), null, true));
                $form->getElement('submit')->setAttribs(array (
                    'class' => 'btn btn-primary',
                ));
                $form->getElement('submit')->setLabel('Make Default');
                $form->getElement('id')->setValue($setting->id);
            }
            $forms['defSettingForm'][] = $form;

        } //initialize forms

        foreach ($this->view->users as $user) {
            $form = new Application_Form_SubmitButton();
            if($user->id == $currentUser->id || $user->getAdminId() == 1) { // is current user or is superuser
                $form->getElement('submit')->setAttribs(array (
                    'class' => 'btn btn-danger disabled',
                ));
                $form->getElement('submit')->setLabel('Delete');
            } else {
                $form->setAction($this->view->url(array('controller' => 'users', 'action' => 'delete'), null, true));
                $form->addAttribs(array(
                    'id' => 'delUserForm' . $user->id,
                    'onsubmit' => self::VALIDATE_FORM . "('delUserForm" . $user->id . "')",
                ));
                $form->getElement('id')->setValue($user->id);
                $form->getElement('submit')->setAttribs(array(
                    'class' => 'btn btn-danger',
                ));
                $form->getElement('submit')->setLabel('Delete');
            }
            $forms['delUserForm'][] = $form;


            $form = new Application_Form_SubmitButton();
            if($user->id == $currentUser->id || $user->getAdminId() == 1 || !$user->verified) {
                $form->getElement('submit')->setAttribs(array (
                    'class' => 'btn btn-primary disabled',
                ));
                $form->getElement('submit')->setLabel('Make Admin');
            } else {
                if($user->getAdminId()) {
                    $form->addAttribs(array(
                        'id' => 'umkUserForm' . $user->id,
                        'onsubmit' => self::VALIDATE_FORM . "('umkUserForm" . $user->id . "')",
                    ));
                    $form->setAction($this->view->url(array('controller' => 'users', 'action' => 'umkadmin'), null, true));
                    $form->getElement('submit')->setAttribs(array(
                        'class' => 'btn btn-primary',
                    ));
                    $form->getElement('submit')->setLabel('Unmake Admin');
                } else {
                    $form->addAttribs(array(
                        'id' => 'mkUserForm' . $user->id,
                        'onsubmit' => self::VALIDATE_FORM . "('mkUserForm" . $user->id . "')",
                    ));
                    $form->setAction($this->view->url(array('controller' => 'users', 'action' => 'mkadmin'), null, true));
                    $form->getElement('submit')->setAttribs(array(
                        'class' => 'btn btn-primary',
                    ));
                    $form->getElement('submit')->setLabel('Make Admin');
                }
                $form->getElement('id')->setValue($user->id);
            }
            $forms['mkUserForm'][] = $form;
        } // initialize forms

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

    public function mycartAction() {

        $categoriesMapper = new Application_Model_CategoryMapper();
        $this->view->categories = $categoriesMapper->fetchAll();

        $this->view->headScript()->appendFile(JS_DIR . '/' . self::PROCESS_PAYMENT . '.js');
        $this->view->headScript()->appendFile(JS_DIR . '/' . self::DELETE_FROM_CART . '.js');
        $this->view->headScript()->appendFile(JS_DIR . '/' . self::COUNT_CART . '.js');
        $this->view->headScript()->appendFile(JS_DIR . '/' . self::ADD_TO_CART . '.js');

        $userMapper = new Application_Model_UserMapper();

        $auth = Zend_Auth::getInstance();
        $currentUser = null;
        if($auth->hasIdentity()) $currentUser = $auth->getIdentity();

        //var_dump($userMapper->getShoppingCart($user_id));
        $auxItem = new Application_Model_CartItem();
        $auxItem->getCurrency()->setService(new My_Class_FakeService());
        $this->view->total = $auxItem->getCurrency();
        $this->view->shoppingcart = $userMapper->getShoppingCart($currentUser);
        $forms = array();
        foreach($this->view->shoppingcart as $i => $product) {
            $upForm = new Application_Form_UpdateCart();
            $upForm->setAction( $this->view->url(array('controller' => 'users','action' => 'updatecart'), null, true))
                 ->setName ("upForm$i");
            $upForm->getElement('product_id')->setValue($product->id);
            $upForm->getElement('quantity')->setValue($product->quantity);
            $forms['upForm'][] = $upForm;

            $delForm = new Application_Form_DelFromCart();
            $delForm->setAction( $this->view->url(array('controller' => 'users','action' => 'delfromcart'), null, true))
                ->setName ("delForm$i");
            $delForm->getElement('product_id')->setValue($product->id);
            $forms['delForm'][] = $delForm;
        }

        $this->view->forms = $forms;

        $this->view->delForm = new Application_Form_DelFromCart();

        $this->_helper->layout->setLayout('shop');
    }

    public function updatecartAction() {
        $request = $this->getRequest();
        $form = new Application_Form_UpdateCart();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $auth = Zend_Auth::getInstance();
                $user_id = '';
                if($auth->hasIdentity()) $user_id = $auth->getIdentity()->id;

                $dbAdapter = Zend_Db_Table::getDefaultAdapter();

                $dbAdapter->update('shoppingcarts', array('quantity' => $data['quantity']), array('user_id = ?' => $user_id, 'product_id = ?' => $data['product_id']));
                //var_dump($user_id, $data);
                return $this->_helper->redirector('mycart');
            } else {
                foreach ($form->getMessages() as $error){
                    $this->_helper->getHelper('FlashMessenger')->addMessage(array_shift(array_values($error)), 'error');
                    $this->_helper->redirector('mycart');
                }
            }
        }
    }

    public function delfromcartAction() {
        $request = $this->getRequest();
        $form = new Application_Form_DelFromCart();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $auth = Zend_Auth::getInstance();
                $user_id = '';
                if($auth->hasIdentity()) $user_id = $auth->getIdentity()->id;

                $dbAdapter = Zend_Db_Table::getDefaultAdapter();

                $dbAdapter->delete('shoppingcarts', array('user_id = ?' => $user_id, 'product_id = ?' => $data['product_id']));
                //var_dump($user_id, $data);
                return $this->_helper->redirector('mycart');
            } else {
                foreach ($form->getMessages() as $error){
                    $this->_helper->getHelper('FlashMessenger')->addMessage(array_shift(array_values($error)), 'error');
                    $this->_helper->redirector('mycart');
                }
            }
        }
    }

    public function deleteAction() {
        $request = $this->getRequest();
        $form = new Application_Form_SubmitButton();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $productMapper = new Application_Model_UserMapper();
                if(isset($data['id']))  {
                    $productMapper->getDbTable()->delete(array('id = ?' => $data['id'], 'hash <> ?' => 'superuser'));
                }
                return $this->_helper->redirector('dashboard');
            }
        }
    }

    public function mkadminAction() {
        $request = $this->getRequest();
        $form = new Application_Form_SubmitButton();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $productMapper = new Application_Model_UserMapper();
                if(isset($data['id']))  $productMapper->mkAdmin($data['id']);
                return $this->_helper->redirector('dashboard');
            }
        }
    }

    public function umkadminAction() {
        $request = $this->getRequest();
        $form = new Application_Form_SubmitButton();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $data = $form->getValues();
                $productMapper = new Application_Model_UserMapper();
                if(isset($data['id']))  $productMapper->umkAdmin($data['id']);
                return $this->_helper->redirector('dashboard');
            }
        }
    }

    public function paypalAction() {


        //Zend_Loader::loadFile('paypal_bootstrap.php', APPLICATION_PATH . "/../library/My/", true);

        require_once (APPLICATION_PATH . "/../library/My/paypal_bootstrap.php");

        $error = false;
        $approvalLink = null;
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $currentUser = $auth->getIdentity();
            $userMapper = new Application_Model_UserMapper();
            $db_adapter = $userMapper->getDbTable()->getAdapter();
            $db = Zend_Db::factory('Mysqli',$db_adapter->getConfig());

            $results = $userMapper->getShoppingCart($currentUser);
            $user = $userMapper->getDbTable()->find($currentUser->id)->current();
            $currencyMapper = new Application_Model_CurrencyMapper();
            $defaultCurrency = $currencyMapper->getDefaultCurrency();

            $data = array(
                'user_id'           => $currentUser->id,
                'state'             => 'created',
                'email'             => $user->email,
            );
            $db->insert('orders', $data);
            $lastOrderId = $db->lastInsertId('orders', 'id');

            $items = array();
            $subTotal = 0;
            foreach($results as $i => $result) {
                $item = new Item();
                $item->setName($result->name)
                    ->setCurrency($defaultCurrency->code)
                    ->setQuantity($result->quantity)
                    ->setSku($i + 1) // Similar to `item_number` in Classic API
                    ->setPrice($result->price);
                    //->setDescription($result->c_id);
                $db->insert('ordered_products', array(
                    'product_id'    => $result->id,
                    'name'          => $result->name,
                    'category_id'   => $result->getCategoryId(),
                    'currency'      => $defaultCurrency->code,
                    'price'         => $result->price,
                    'quantity'      => $result->quantity,
                    'order_id'      => $lastOrderId));

                $items[] = $item;
                $subTotal += $result->quantity * (float)number_format($result->price,2);
            }

            $itemList = new ItemList();
            $itemList->setItems($items);

            $shippingTax = 0; //

            $details = new Details();
            $details->setShipping($shippingTax)
                ->setTax(0)
                ->setSubtotal($subTotal);
            $total = $shippingTax + $subTotal;

            $amount = new Amount();
            $amount->setCurrency($defaultCurrency->code)
                ->setTotal($total)
                ->setDetails($details);

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setCustom($lastOrderId)
                ->setItemList($itemList)
                ->setDescription("Payment description")
                ->setInvoiceNumber(uniqid());

            $baseUrl = getBaseUrl();
            $redirectUrls = new RedirectUrls();
            $redirectUrls->setReturnUrl($baseUrl . '/users/exepaypal/?success=true');
            $redirectUrls->setCancelUrl($baseUrl .'/users/exepaypal/?cancel=true');

            $payment = new Payment();
            $payment->setIntent("sale");
            $payment->setPayer($payer);
            $payment->setRedirectUrls($redirectUrls);
            $payment->setTransactions(array($transaction));

            try {
                $result = $payment->create($apiContext);

            }
            catch (Exception $ex) {
                $error = true;
            }

            $approvalLink = $payment->getApprovalLink();
        }
        else $error = true;


        $response = array(
            'error' => $error,
            'approvalLink' => $approvalLink
        );

        //Send as JSON
        header("Content-Type: application/json", true);

        //Return JSON
        echo json_encode($response);

        //Stop Execution
        exit;
    } //call from ajax

    public function exepaypalAction(){
        if (isset($_GET['success']) && $_GET['success'] == 'true') {
            $paymentId = $_GET['paymentId'];
            $token = $_GET['token'];
            $PayerID = $_GET['PayerID'];

            require_once (APPLICATION_PATH . "/../library/My/paypal_bootstrap.php");

            $payment = Payment::get($paymentId, $apiContext);
            $execution = new PaymentExecution();
            $execution->setPayerId($PayerID);

            $result = $payment->execute($execution, $apiContext);

            $payment = Payment::get($paymentId, $apiContext);

            if ($payment->getState() == 'approved') {

                $transactions = $payment->getTransactions();
                $relatedResources = $transactions[0]->getRelatedResources();
                $sale = $relatedResources[0]->getSale();
                $saleId = $sale->getId();

                $sale = Sale::get($saleId, $apiContext);

                //var_dump($transactions[0], $sale);
                $order_id = $transactions[0]->getCustom();

                $userMapper = new Application_Model_UserMapper();
                $db_adapter = $userMapper->getDbTable()->getAdapter();
                $db = Zend_Db::factory('Mysqli',$db_adapter->getConfig());
                $data = array(
                    'state'         => $sale->getState(),
                    'transaction_id'=> $saleId,
                );

                $db->update('orders', $data, array('id = ?' => $order_id));
                $row = $db->fetchRow($db->select('user_id')->from('orders')->where('id = ?', $order_id));

                $db->delete('shoppingcarts',array('user_id = ?' => $row['user_id']));

                $this->_helper->getHelper('FlashMessenger')->addMessage('Order Complete', 'success');
            };
        }
        else {
            $this->_helper->getHelper('FlashMessenger')->addMessage('You close the payment', 'error');
        }
        return $this->_helper->redirector('mycart');
    }
}