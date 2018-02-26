<?php

/**
 * Custom
 * 
 * LICENSE:
 * 
 * @category   Custom
 * @package    Custom
 * @subpackage Controller
 * @copyright  Copyright (c)  KenJi
 * @license    
 * @version    
 */
/**
 * Zend_Controller_Plugin_Abstract
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * Implement the privilege controller.
 * 
 * @category   Custom
 * @package    Custom
 * @subpackage Controller
 * @author     
 * @copyright  Copyright (c)  KenJi
 * @license    
 */
class Custom_Controller_Plugin_KenAuth extends Zend_Controller_Plugin_Abstract {

    /**
     * An instance of Zend_Auth
     * @var Zend_Auth
     */
    private $_auth;

    /**
     * An instance of Custom_Acl
     * @var Custom_Acl
     */
    private $_acl;

    /**
     * Redirect to a new controller when the user has a invalid indentity.
     * @var array
     */
    private $_noauth = array('module' => 'default',
        'controller' => 'auth',
        'action' => 'login');

    /**
     * Redirect to 'error' controller when the user has a vailid identity 
     * but no privileges
     * @var array
     */
    private $_nopriv = array('module' => 'default',
        'controller' => 'error',
        'action' => 'privileges');

    /**
     * Constructor.
     * @return void
     */
    public function __construct($auth, $acl) {
        $this->_auth = $auth;
        $this->_acl = $acl;
    }

    /**
     * Track user privileges.
     * @param Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        if ($this->_auth->hasIdentity()) {
            $role = $this->_auth->getIdentity()->role;
        } else {
            $role = 'guest';
        }
        $module = $request->module;
        $controller = $request->controller;
        $action = $request->action;
        $resource = "$module:$controller";
        if (!$this->_acl->has($resource)) {
            $resource = null;
        }

        if (!$this->_acl->isAllowed($role, $resource, $action)) {
            if (!$this->_auth->hasIdentity()) {
                $module = $this->_noauth['module'];
                $controller = $this->_noauth['controller'];
                $action = $this->_noauth['action'];
            } else {
                $module = $this->_nopriv['module'];
                $controller = $this->_nopriv['controller'];
                $action = $this->_nopriv['action'];
            }
        }

        $request->setModuleName($module);
        $request->setControllerName($controller);
        $request->setActionName($action);
    }

}
