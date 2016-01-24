<?php

class My_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    protected $_defaultRole = 'guest';

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();

        $acl = new My_Class_Acl();

        if($auth->hasIdentity()) {
            $user = $auth->getIdentity();
            if(!$acl->isAllowed($user->role, $request->getControllerName(), $request->getActionName())) {

                $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
                $flashMessenger->addMessage('You are not authorized to view this page. Login with other user name who has privileges !','error');

                return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setGotoUrl('auth/login');
            }
        } else {
            if(!$acl->isAllowed($this->_defaultRole, $request->getControllerName(), $request->getActionName())) {

                $flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
                $flashMessenger->addMessage('You are not authorized to view this page. Login with other user name who has privileges !','error');

                return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setGotoUrl('auth/login');
            }
        }
    }
}