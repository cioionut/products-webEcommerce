<?php

class Zend_View_Helper_MyCart extends Zend_View_Helper_Abstract
{
    public function myCart ()
    {
        $cart = "<a class='btn btn-info' type ='button' href = '/users/mycart'>
                <span class='badge' style='color:red' id = 'countCart' ></span> My Shopping Cart <span class='badge' id = 'myCart' ></span>
                </a> ";
        return $cart;
    }
}