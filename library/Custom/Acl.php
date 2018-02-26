<?php

/**
 * Custom
 * 
 * LICENSE:
 * 
 * @category   Custom
 * @package    Custom
 * @copyright  Copyright (c)
 * @license    
 * @version    
 */


/**
 * Zend_Acl
 */
require_once 'Zend/Acl.php';

/**
 * Zend_Acl_Role
 */
require_once 'Zend/Acl/Role.php';

/**
 * Zend_Acl_Resource
 */
require_once 'Zend/Acl/Resource.php';


/**
 * Access Control List (ACL)
 *
 * @category   Custom
 * @package    Custom
 * @author     
 * @copyright  Copyright (c)
 * @license
 */
class Custom_Acl extends Zend_Acl
{
    /**
     * Constructor.
     *
     * @return void
     */
	public function __construct()
    {
        // Add resource
        $resource = new Zend_Config_Ini('../app/config/resource.ini', null);
        foreach ($resource->toArray() as $key_o => $arr) {
        	$this->add(new Zend_Acl_Resource($key_o));
        	foreach ($arr as $key_i => $value) {
        	    $this->add(new Zend_Acl_Resource($value), $key_o);
        	}
        }
        
    	// Add role
        $this->addRole(new Zend_Acl_Role('guest')); 
        $this->addRole(new Zend_Acl_Role('member'), 'guest');
        $this->addRole(new Zend_Acl_Role('admin'));
        
        // Assign rule
        $this->allow(null, 'default:auth');
        
        $this->allow('guest',  'default:index', 'index');
        $this->allow('member', 'default:index', array('add', 'edit', 'delete'));
        
        $this->allow('admin');
        
    }
}