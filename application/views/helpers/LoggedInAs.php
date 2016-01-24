<?php

class Zend_View_Helper_LoggedInAs extends Zend_View_Helper_Abstract
{
    public function loggedInAs ()
    {
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {

            //var_dump($auth->getIdentity());

            $username = $auth->getIdentity()->email;
            $logoutUrl = $this->view->url(array('controller'=>'auth',
                'action'=>'logout'), null, true);
            return "<a href = '$logoutUrl'>Logout</a>";
        }

        $request = Zend_Controller_Front::getInstance()->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        if($controller == 'auth' && $action == 'login') {
            return '';
        }
        $loginUrl = $this->view->url(array('controller'=>'auth', 'action'=>'login'));

        return "<a href = '$loginUrl'>LogIn</a>";;
    }
}