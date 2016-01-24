<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initDoctype(){
		$this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('HTML5');
	}

	/*protected function _initAppAutoload()
	{
		$autoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath' => dirname(__FILE__),
		));
		return $autoloader;
	}*/


	protected function setConstants($constants)
	{
		foreach($constants as $name => $value)
		{
			if(!defined($name))
				define($name, $value);
		}

	}
/*
	protected function _initRoutes()
	{

		$router = Zend_Controller_Front::getInstance()->getRouter();

		$loginRoute = new Zend_Controller_Router_Route('login', array('controller' => 'auth', 'action' => 'login'));

		$logoutRoute = new Zend_Controller_Router_Route('logout', array('controller' => 'auth', 'action' => 'logout'));

		$routesArray = array('login' => $loginRoute, 'logout' => $logoutRoute);

		$router->addRoutes($routesArray);

	}*/

	protected function _initPlugins()
	{
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->registerPlugin(new My_Plugin_SessionTrack());
		$frontController->registerPlugin(new My_Plugin_Auth());
	}


	protected function _initSessions() {
		$sessionconf = APPLICATION_PATH . '/' . 'configs' . '/' . 'sessionconf.ini';
		$config = new Zend_Config_Ini($sessionconf, 'development');
		Zend_Session::setOptions($config->toArray());
	}

}

