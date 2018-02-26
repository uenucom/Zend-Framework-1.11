<?php

/**
 * Custom
 * 
 * LICENSE:
 * 
 * @category   Custom
 * @package    Custom
 * @subpackage Controller
 * @copyright  Copyright (c)
 * @license    
 * @version    
 */
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';

/**
 * Custom super action controller.
 *
 * @category   Custom
 * @package    Custom
 * @subpackage Controller
 * @author     
 * @copyright  Copyright (c)
 * @license    
 */
class Custom_Controller_Action extends Zend_Controller_Action {

    /**
     * Pre-dispatch routines
     *
     * Authenticate user whether or login
     * 
     * @return void
     */
    function preDispatch() {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            $this->_redirect('auth/login');
        }
    }

}
