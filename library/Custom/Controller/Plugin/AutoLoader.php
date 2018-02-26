<?php

class Custom_Controller_Plugin_AutoLoader extends Zend_Controller_Plugin_Abstract {

    public function preDispatch(Zend_Controller_Request_Abstract $request) {
        $module = $request->module;  //get modules value
        StElog::pushNotice('post_params', json_encode($request->getParams()));
        StElog::pushNotice('module_name', $module);
        Zend_Registry::set('module_name', $module);
        if ($module !== 'default') {
            $perLoadModule = 'default';
            self::loadFile($perLoadModule);
        }
//        loadDefaultDB();
        self::loadDB($module);
        self::loadFile($module);
        self::loadConfig($module);
    }

    public function loadDefaultDB() {
        //数据库配置信息
        
    }

    public function loadDB($module) {
        //数据库配置信息
        $dbconfig = new Zend_Config_Ini(APPLICATION_PATH . "/modules/{$module}/configs/" . APPLICATION_ENV . "/database.ini", null, true);
        if (in_array(APPLICATION_ENV, array("production"))) {
            Zend_Registry::set('dbconfig', $dbconfig->online);
        } elseif (APPLICATION_ENV === 'development') {
            Zend_Registry::set('dbconfig', $dbconfig->offline);
        } elseif (APPLICATION_ENV === 'testing') {
            Zend_Registry::set('dbconfig', $dbconfig->testing);
        }
    }

    //动态加载文件
    public static function loadFile($module) {
        $load_module_path = APPLICATION_PATH . "/modules/{$module}/models";
        set_include_path(implode(PATH_SEPARATOR, array(
            realpath($load_module_path),
            get_include_path(),
        )));
    }

    //动态加载文件
    public static function loadConfig($module) {
        $filename = APPLICATION_PATH . "/modules/{$module}/configs/" . APPLICATION_ENV . "/config.php";
        if (is_file($filename)) {
            require_once $filename;
        }
    }

    public function loadMenu($module) {
        
    }

}
