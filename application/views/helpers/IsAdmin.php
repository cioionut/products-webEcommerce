<?php

class Zend_View_Helper_IsAdmin extends Zend_View_Helper_Abstract
{

    public function isAdmin ()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $user = $auth->getIdentity();
            return  ($user->role == 'admin')? true : false;
        }
        return false;
    }
}