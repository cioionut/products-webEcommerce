<?php

class Application_Model_Mailsetting {
    private $id;
    private $host;
    private $port;
    private $stype;
    private $email;
    private $password;
    private $default_config;
    private $json_config;

    public function __construct($params = NULL){
        if(isset($params['id'])){
            $this->id = $params['id'];
        }
        if(isset($params['email'])){
            $this->email = $params['email'];
        }
        if(isset($params['password'])){
            $this->password = $params['password'];
        }
        if(isset($params['host'])){
            $this->host = $params['host'];
        }
        if(isset($params['stype'])){
            $this->stype = $params['stype'];
        }
        if(isset($params['port'])){
            $this->port = $params['port'];
        }
        if(isset($params['default_config'])){
            $this->default_config = $params['default_config'];
        }
        if(isset($params['json_config'])){
            $this->json_config = $params['json_config'];
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
    public function getJsonConfig()
    {
        return $this->json_config;
    }

    /**
     * @param mixed $json_config
     */
    public function setJsonConfig($json_config)
    {
        $this->json_config = $json_config;
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
    public function getDefaultConfig()
    {
        return $this->default_config;
    }

    /**
     * @param mixed $default_config
     */
    public function setDefaultConfig($default_config)
    {
        $this->default_config = $default_config;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getStype()
    {
        return $this->stype;
    }

    /**
     * @param mixed $stype
     */
    public function setStype($stype)
    {
        $this->stype = $stype;
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