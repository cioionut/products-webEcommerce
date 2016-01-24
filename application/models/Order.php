<?php

class Application_Model_Order
{

    private $id;
    private $user_id;
    private $transaction_id;
    private $state;
    private $email;
    private $create_date;

    public function __construct($params = NULL)
    {

        if (isset($params['id']) && !empty($params['id'])) {
            $this->id = $params['id'];
        }
        if (isset($params['user_id']) && !empty($params['user_id'])) {
            $this->user_id = $params['user_id'];
        }
        if (isset($params['state']) && !empty($params['state'])) {
            $this->state = $params['state'];
        }
        if (isset($params['email']) && !empty($params['email'])) {
            $this->email = $params['email'];
        }
        if (isset($params['create_date']) && !empty($params['create_date'])) {
            $this->create_date = $params['create_date'];
        }
        if (isset($params['transaction_id']) && !empty($params['transaction_id'])) {
            $this->transaction_id = $params['transaction_id'];
        }
    }


    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid product property');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid product property');
        }
        return $this->$method();
    }

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * @param mixed $transaction_id
     */
    public function setTransactionId($transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * @param mixed $create_date
     */
    public function setCreateDate($create_date)
    {
        $this->create_date = $create_date;
    }

}