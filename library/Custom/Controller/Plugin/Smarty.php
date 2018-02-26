<?php

/**
 * Zend_Controller_Plugin_Abstract
 */
//require_once 'Zend/Controller/Plugin/Abstract.php';
class Custom_Controller_Plugin_Smarty extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        /**
          $module = $request->getModuleName();
          $controller = $request->getControllerName();
          $action = $request->getActionName();
         */
        //$module = $this->_request->module;//获取模块名称
        $module = $request->module;  //get modules value
        $view = Zend_Registry::get('smarty');
        $view->setBasePath('../application/modules/' . $module);
        //$this -> setThisMoudelName($module);
    }

    public function setThisMoudelName($name) {
        $this->ThisMoudelName = $name;
    }

    public function getThisMoudelName() {
        return $this->ThisMoudelName;
    }

}