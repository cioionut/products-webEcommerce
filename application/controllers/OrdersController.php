<?php

class OrdersController extends Zend_Controller_Action{

    public function indexAction() {
        $this->_helper->redirector('dashboard', 'users');
    }

    public function stupdateAction() {
        require_once (APPLICATION_PATH . "/../library/My/paypal_bootstrap.php");

        $orderMapper = new Application_Model_OrderMapper();
        $orders = $orderMapper->fetchAll();
        $ordStates = array();
        foreach($orders as $order) {
            if($order->state != 'completed' && $order->getTransactionId()) {
                $ordStates[] = array(
                    'id'    => $order->id,
                    'state' => $orderMapper->stupdate($order->getTransactionId(), $apiContext),
                );
            }
        }
        //Send as JSON
        header("Content-Type: application/json", true);

        //Return JSON
        echo json_encode($ordStates);

        //Stop Execution
        exit;
    }

    public function saveAction(){

    }

    public function deleteAction(){

    }

    public function mkdefaultAction(){

    }
}