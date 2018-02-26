<?php

/**
 * Custom
 * 
 * LICENSE:
 * 
 * @category   Custom
 * @package    Custom
 * @subpackage View
 * @copyright  Copyright (c)
 * @license    
 * @version    
 */


/**
 * Zend_View_Interface
 */
require_once 'Zend/View/Interface.php';

/**
 * Smarty
 */
require_once 'Smarty/Smarty.class.php';


/**
 * Custom View
 *
 * @category   Custom
 * @package    Custom
 * @subpackage View
 * @copyright  Copyright (c)
 * @license
 */
class Custom_View_Smarty implements Zend_View_Interface
{
    /**
     * Smarty object
     * @var Smarty
     */
    protected $_smarty;
    
    /**
     * Constructor.
     *
     * @param array $extraParams
     * @return void
     */
    public function __construct($extraParams = array())
    {
    	$this->_smarty = new Smarty;

    	foreach ($extraParams as $key => $value) {
    		$this->_smarty->$key = $value;
    	}
    }
    
    /**
     * Return the template engine object.
     *
     * @return Smarty
     */
    public function getEngine()
    {
    	return $this->_smarty;
    }

    /**
     * Sets the base directory path to templates.
     *
     * @param   string  $path
     * @return  Custom_View_Smarty
     */
    public function setBasePath($path, $classPrefix = 'Zend_View')
    {
        $path      = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
        $parentDir = dirname($path) . DIRECTORY_SEPARATOR;
        
        $this->setTemplateDir($path . 'scripts');
        $this->setCompileDir($parentDir . '_templates_c');
        $this->setCacheDir($parentDir . '_cache');
        return $this;
    }

    /**
     * Alias of setBasePath() method.
     *
     * @param   string  $path
     * @return  Custom_View_Smarty
     */
    public function addBasePath($path, $classPrefix = 'Zend_View')
    {
        $this->setBasePath($path);
        return $this;
    }

    /**
     * Sets the directory path to templates.
     *
     * @param   string  $path
     * @return  Custom_View_Smarty
     */
    public function setTemplateDir($path)
    {
        if (is_dir($path) && is_readable($path)) {
            $this->_smarty->template_dir = $path;
            return $this;
        }
        throw new Exception('Invalid path provided');
    }

    /**
     * Sets the directory path to compiled templates.
     *
     * @param   string  $path
     * @return  Custom_View_Smarty
     */
    public function setCompileDir($path)
    {
        if (is_dir($path) && is_writable($path)) {
            $this->_smarty->compile_dir = $path;
            return $this;
        }
        throw new Exception('Invalid path provided');
    }
    
    /**
     * Sets the directory path to cache templates.
     *
     * @param   string  $path
     * @return  Custom_View_Smarty
     */
    public function setCacheDir($path)
    {
        if (is_dir($path) && is_writable($path)) {
            $this->_smarty->cache_dir = $path;
            return $this;
        }
        throw new Exception('Invalid path provided');
    }
    
    /**
     * Alias of setTemplateDir() method.
     *
     * @param   string  $path
     * @return  Custom_View_Smarty
     */
    public function setScriptPath($path)
    {
        $this->setTemplateDir($path);
        return $this;
    }
    
    /**
     * Returns an array of the directory path to templates.
     *
     * @return  array
     */
    public function getScriptPaths()
    {
        return array($this->_smarty->template_dir);
    }
    
    /**
     * Assign a variable to the template
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     */
    public function __set($key, $val)
    {
    	$this->_smarty->assign($key, $val);
    }
    
    /**
     * Retrieve an assigned variable
     *
     * @param string $key The variable name.
     * @return mixed The variable value.
     */
    public function __get($key)
    {
    	return $this->_smarty->get_template_vars($key);
    }
    
    /**
     * Allows testing with empty() and isset() to work
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
    	return (null !== $this->_smarty->get_template_vars($key));
    }
    
    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
    	$this->_smarty->clear_assign($key);
    }
    
    /**
     * Assign variables to the template
     *
     * Allows setting a specific key to the specified value, OR passing an array
     * of key => value pairs to set en masse.
     *
     * @see __set()
     * @param string|array $spec The assignment strategy to use (key or array of key
     * => value pairs)
     * @param mixed $value (Optional) If assigning a named variable, use this
     * as the value.
     * @return void
     */
    public function assign($spec, $value = null)
    {
    	if (is_array($spec)) {
    		$this->_smarty->assign($spec);
    		return;
    	}
    	
    	$this->_smarty->assign($spec, $value);
    }
    
    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via {@link assign()} or
     * property overloading ({@link __get()}/{@link __set()}).
     * 
     * @return void
     */
    public function clearVars()
    {
    	$this->_smarty->clear_all_assign();
    }
    
    /**
     * Processes a template and returns the output.
     *      
     * @param string $name The template to process.
     * @return string The output.
     */
    public function render($name)
    {
    	return $this->_smarty->fetch($name);
    }
     /**
     * 设置是否生成缓存
     * 如果没有参数,默认为true
     */
    public function setCache($bool){
         if (isset($bool)) {
            $this->_smarty->caching = $bool;
            return;
        }
    }
}