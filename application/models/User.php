<?php

class Application_Model_User {
    private $id;
    private $email;
    private $password;
    private $currency_id;
    private $hash;
    private $verified;
    private $adminId;


    public function __construct($params = NULL)
    {
        if(isset($params['id']) && !empty($params['id'])){
            $this->id = $params['id'];
        }
        if(isset($params['email']) && !empty($params['email'])){
            $this->email = $params['email'];
        }
        if(isset($params['password']) && !empty($params['password'])){
            $this->password = $params['password'];
        }
        if(isset($params['hash']) && !empty($params['hash'])){
            $this->hash = $params['hash'];
        }
        if(isset($params['verified']) && !empty($params['verified'])){
            $this->verified = $params['verified'];
        }
        if(isset($params['verified']) && !empty($params['verified'])){
            $this->verified = $params['verified'];
        }
        if(isset($params['currency_id']) && !empty($params['currency_id'])){
            $this->currency_id = $params['currency_id'];
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
    public function getCurrencyId()
    {
        return $this->currency_id;
    }

    /**
     * @param mixed $currency_id
     */
    public function setCurrencyId($currency_id)
    {
        $this->currency_id = $currency_id;
    }
    /**
     * @return mixed
     */
    public function getAdminId()
    {
        return $this->adminId;
    }

    /**
     * @param mixed $adminId
     */
    public function setAdminId($adminId)
    {
        $this->adminId = $adminId;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getVerified()
    {
        return $this->verified;
    }

    /**
     * @param mixed $verified
     */
    public function setVerified($verified)
    {
        $this->verified = $verified;
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
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
}