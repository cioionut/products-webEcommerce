<?php

class AuthController  extends Zend_Controller_Action  {

    const REMEMBER_DAYS = 1;
    const SECONDS_IN_DAY = 86400;

    const SECONDS_IN_MINUTE = 60;
    const EXPIRATION_MINUTES = 5;


    public function signupAction(){
        $form = new Application_Form_SignUp();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $data = $form->getValues();
                $data['password'] = md5($data['password1']); // MD5
                unset($data['password1']);
                unset($data['password2']);
                $data['hash'] = md5(rand(1, 1000));
                $currencyMapper = new Application_Model_CurrencyMapper();
                $currency = $currencyMapper->findByCode($data['currency_code']);
                if(!$currency->id) $data['currency_id'] = $currencyMapper->getDefaultCurrency()->id;
                else $data['currency_id'] = $currency->id;
                $user = new Application_Model_User($data);

                $userMapper = new Application_Model_UserMapper();

                try {
                    $userMapper->insert($user);

                    $mailMapper = new Application_Model_MailsettingMapper();
                    $default_config_id = $mailMapper->getDefault();
                    $mailsetting = $mailMapper->getConfig($default_config_id);

                    $obj = new My_Class_Cript();

                    $config = array('auth' => 'login',
                        'username' => $mailsetting->email,
                        'password' => $obj->decript($mailsetting->password),
                        'ssl' => $mailsetting->stype,
                        'port' => $mailsetting->port,
                    );

                    $transport = new Zend_Mail_Transport_Smtp($mailsetting->host, $config);

                    $mail = new Zend_Mail();

                    $message = "<p>
                                Thanks for signing up!
                                Your account has been created, activate your account by pressing the url below.
                            </p>
                            <p>-----------------------</p>
                            <p><a href='http://" . SITE_NAME . "/auth/verify?email=" . $user->email . "&hash=" . $user->hash . "'>Click Here</a> to activate your account</p>";

                    $mail->setBodyHtml($message);
                    $mail->setFrom('noreply@products-pilot.loc', 'Products-Pilot');
                    $mail->addTo($data['email'], 'You');
                    $mail->setSubject('Account Validation');

                    if ($mail->send($transport)) {
                        $this->_helper->getHelper('FlashMessenger')->addMessage('Your account has been made please verify it by clicking the activation link that has been send to your email.', 'info');
                        return $this->_helper->redirector('login');
                    } else {
                        $userMapper->delete($user);
                    }
                }
                catch (Exception $e){
                    $message = $e->getMessage();
                    if ($e instanceof Zend_Db_Statement_Mysqli_Exception) {
                        if ($e->getCode() == 1062) $message = 'This email is already registered';
                        else $message = 'Something goes wrong';
                    } else {
                        $userMapper->delete($user);
                        $message = 'Mail service error: ' . $e->getMessage();
                    }
                    $this->_helper->getHelper('FlashMessenger')->addMessage($message, 'error');
                    $this->_helper->redirector('signup');
                }
            }
            else{
                foreach ($form->getMessages() as $error){
                    $this->_helper->getHelper('FlashMessenger')->addMessage(array_shift(array_values($error)), 'error');
                    $this->_helper->redirector('signup');
                    //var_dump(array_shift(array_values($error)));
                }
            }
        }
        $this->view->form = $form;
    }

    public function verifyAction(){
        $email = $this->getParam('email');
        $hash = $this->getParam('hash');
        $info = '';
        if( $email && $hash) {
            $userMapper = new Application_Model_UserMapper();
            if ($userMapper->verify($email, $hash)) {
                $info = 'Account was veriffied';
            }
            else {
                $info = 'This url in not valid';
            }
        }
        else {
            $info = 'This url in not valid';
        }
        $this->_helper->getHelper('FlashMessenger')->addMessage($info, 'info');
        $this->_helper->redirector('login');

    }

    public function loginAction() {

        $form = new Application_Form_Login();
        $request = $this->getRequest();
        if ($request->isPost()) {

            if ($form->isValid($request->getPost())) {

                $result = $this->_process($form->getValues());
                if ($result->isValid()) {
                    if ($form->getValue('rememberMe') == 1) {
                        // REMEMBER THE SESSION FOR 1 DAYS
                        Zend_Session::rememberMe(self::REMEMBER_DAYS * self::SECONDS_IN_DAY);  // 1 days
                    } else {
                        // DO NOT REMEMBER THE SESSION
                        Zend_Session::forgetMe();
                        $session = new Zend_Session_Namespace('Zend_Auth');
                        $session->setExpirationSeconds(self::SECONDS_IN_MINUTE * self::EXPIRATION_MINUTES);
                    }
                    // We're authenticated! Redirect to the home page
                    $this->_helper->redirector('index', 'index');
                }
                else {
                    foreach ($result->getMessages() as $message) {
                        $this->_helper->getHelper('FlashMessenger')->addMessage($message,'error');
                        $this->_helper->redirector('login');
                    }
                }
            }
        }
        $this->view->form = $form;
    }

    protected function _process($values)
    {
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($values['email']);
        $adapter->setCredential($values['password']);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $user = $adapter->getResultRowObject(null, array('password', 'hash', 'verified'));

            $user->role = $this->_getRole($user->id);

            $auth->getStorage()->write($user);
        }
        return $result;
    }

    protected function _getAuthAdapter() {

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('users')
            ->setIdentityColumn('email')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('MD5(?) and verified = 1');

        return $authAdapter;
    }

    public function logoutAction()
    {
        Zend_Session::forgetMe();
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->getHelper('FlashMessenger')->addMessage('You are now logged out','success');
        $this->_helper->redirector('login', 'auth'); // back to login page
    }

    protected function _getRole($user_id){

        $userMapper = new Application_Model_UserMapper();
        $db_adapter = $userMapper->getDbTable()->getAdapter();
        $db = Zend_Db::factory('Mysqli',$db_adapter->getConfig());
        $admin = $db->fetchRow($db->select('id')->from('admins')->where('user_id = ?', $user_id));


        if($admin){
            return 'admin';
        }
        return 'user';
    }

    public function chpassAction() {
        $form = new Application_Form_ChPass();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $data = $form->getValues();
                $data['oldpassword'] = md5($data['oldpassword']); // MD5
                $data['newpassword'] = md5($data['newpassword1']);
                unset($data['newpassword1']);
                unset($data['newpassword2']);

                $userMapper = new Application_Model_UserMapper();

                try{
                    $fields = array(
                        'password' => $data['newpassword'],
                    );
                    $userMapper->getDbTable()->update($fields, array('password = ?' => $data['oldpassword']));
                    $this->_helper->getHelper('FlashMessenger')->addMessage("Password Changed", 'success');
                    $this->_helper->redirector('chpass');
                }
                catch(Exception $e){
                    $this->_helper->getHelper('FlashMessenger')->addMessage($e->getMessage(), 'error');
                    $this->_helper->redirector('chpass');
                }

            }
            else{
                foreach ($form->getMessages() as $error){
                    $this->_helper->getHelper('FlashMessenger')->addMessage(array_shift(array_values($error)), 'error');
                    $this->_helper->redirector('chpass');
                }
            }
        }
        $this->view->form = $form;
    }

    public function resetpassAction() {
        $form = new Application_Form_ResetPass();
        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                $data = $form->getValues();

                try {
                    //check if email is registered
                    $userMapper = new Application_Model_UserMapper();
                    $result = $userMapper->getDbTable()->fetchRow($userMapper->getDbTable()->select('id')->where('email = ?', $data['email']));
                    if( !$result || count($result) == 0 ) throw new ErrorException('Email is not registered!');

                    //generate new password and update database field
                    $length = 8;
                    $pass = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
                    $new_pass = md5($pass);

                    $update_fields = array(
                        'password'  => $new_pass,
                    );

                    //send email with new credentials
                    $mailMapper = new Application_Model_MailsettingMapper();
                    $default_config_id = $mailMapper->getDefault();
                    $mailsetting = $mailMapper->getConfig($default_config_id);

                    $obj = new My_Class_Cript();

                    $config = array('auth' => 'login',
                        'username' => $mailsetting->email,
                        'password' => $obj->decript($mailsetting->password),
                        'ssl' => $mailsetting->stype,
                        'port' => $mailsetting->port,
                    );

                    $transport = new Zend_Mail_Transport_Smtp($mailsetting->host, $config);

                    $mail = new Zend_Mail();

                    $message = "<p>New password is: $pass</p>";

                    $mail->setBodyHtml($message);
                    $mail->setFrom('noreply@products-pilot.loc', 'Products-Pilot');
                    $mail->addTo($data['email'], 'You');
                    $mail->setSubject('New Password');

                    if ($mail->send($transport)) {
                        $result = $userMapper->getDbTable()->update($update_fields, array('email = ?' => $data['email']));
                        if( !$result ) throw new ErrorException('Something goes wrong!');
                        $this->_helper->getHelper('FlashMessenger')->addMessage('Check your email for new password', 'info');
                        return $this->_helper->redirector('login');
                    }
                }
                catch (Exception $e){
                    //var_dump($e);
                    if ($e instanceof ErrorException) {
                        $message = $e->getMessage();
                    } else {
                        $message = 'Mail service error: ' . $e->getMessage();
                    }
                    $this->_helper->getHelper('FlashMessenger')->addMessage($message, 'error');
                    $this->_helper->redirector('resetpass');
                }

                //$this->_helper->redirector('login');
            }
            else{
                foreach ($form->getMessages() as $error){
                    $this->_helper->getHelper('FlashMessenger')->addMessage(array_shift(array_values($error)), 'error');
                    $this->_helper->redirector('resetpass');
                    //var_dump(array_shift(array_values($error)));
                }
            }
        }
        $this->view->form = $form;
    }
}