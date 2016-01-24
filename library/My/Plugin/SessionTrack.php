<?php

class My_Plugin_SessionTrack extends Zend_Controller_Plugin_Abstract {

    const SECONDS_IN_MINUTE = 60;
    const EXPIRATION_MINUTES = 5;

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) { // user is logged in
            // get an instance of Zend_Session_Namespace used by Zend_Auth
            $session = new Zend_Session_Namespace($auth->getStorage()->getNamespace());

            /*$timeLeftTillSessionExpires = $_SESSION['__ZF']['Zend_Auth']['ENT'] - time(); */

            if (isset($_SESSION['__ZF']['Zend_Auth']['ENT'])) {
                //var_dump($_SESSION['__ZF']['Zend_Auth']['ENT'] - time());
                $session->setExpirationSeconds(self::SECONDS_IN_MINUTE * self::EXPIRATION_MINUTES);
            }
        }
    }
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {

    }
}