<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    public function _initDefine() {
        $setarray = $this->getOptions();
        //Zend_Session::setOptions($setarray['phpconfig']);
        define('PaginatorDefaultScrollingStyle', $setarray['Paginator']['DefaultScrollingStyle']); //分页风格
        define('PaginatorItemCountPerPage', $setarray['Paginator']['ItemCountPerPage']); //每页显示视频数量
        define('PaginatorPageRange', $setarray['Paginator']['PageRange']); //每页显示分页数量
    }

    //自动加载
    public function _initLoader() {
        Zend_Loader_Autoloader::getInstance()
                ->registerNamespace('Zend_') //Zend框架的命名空间
                ->registerNamespace('Custom_') //插件的命名空间
                ->setFallbackAutoloader(true); //能够载入无命名空间的类，e.g:models目录下的类。

        $setarray = $this->getOptions();
        //Smarty 配置
        $smarty = new Custom_View_Smarty($setarray['smarty']);
        Zend_Registry::set('smarty', $smarty);

        $dbconfig = new Zend_Config_Ini(APPLICATION_PATH . "/modules/default/configs/" . APPLICATION_ENV . "/database.ini", null, true);
        if (in_array(APPLICATION_ENV, array("production"))) {
            $dbconfig_env = $dbconfig->online;
        } elseif (APPLICATION_ENV === 'development') {
            $dbconfig_env = $dbconfig->offline;
        } elseif (APPLICATION_ENV === 'testing') {
            $dbconfig_env = $dbconfig->testing;
        }
        $dbAdapter = Zend_Db::factory($dbconfig_env->db->adapter, $dbconfig_env->db->config->toArray());
        $config = array(
            'db' => $dbAdapter,
            'name' => 'softrpc_user_info_session',
            'primary' => array(
                'session_id',
                'save_path',
                'name',
            ),
            'primaryAssignment' => array(
                'sessionId', 
                'sessionSavePath',
                'sessionName', 
            ),
            'modifiedColumn' => 'modified',
            'dataColumn' => 'session_data',
            'lifetimeColumn' => 'lifetime',
        );
        Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($config));
        Zend_Session::start();
        $session_id = Zend_Session::getId();
        $auth_info = Zend_Auth::getInstance()->getIdentity();
        if (isset($auth_info) && $auth_info->user_id > 0 && $auth_info->user_name != "") {
            $sessioninfo = SessionList::getInfoList($dbAdapter, $session_id);
            $set_data = array();
            $set_data['uid'] = $auth_info->user_id;
            $set_data['user_name'] = $auth_info->user_name;
            $set_data['user_realname'] = $auth_info->user_realname;
            $set_data['ip'] = Custom_Controller_Plugin_Ipaddress::getIP();
            $set_data['ua'] = $_SERVER['HTTP_USER_AGENT'];
            $set_data['encryption'] = md5($set_data['ip'] . $set_data['ua']);
            if (!empty($sessioninfo)) {
                $key_list = array('uid', 'user_name', 'user_realname', 'ip', 'ua', 'encryption');
                foreach ($key_list as $sub_arr) {
                    if ($sessioninfo[$sub_arr] != $set_data[$sub_arr]) {
                        Zend_Auth::getInstance()->clearIdentity();
                        Zend_Session::destroy();
                    }
                }
            } else {
                SessionList::updateInfoBySId($dbAdapter, $session_id, $set_data);
            }
            SessionList::del($dbAdapter);
        }
    }

    public function _initRset() {
        
    }

    public function _initRpc() {
        
    }

    public function _initSlog() {
        define('GUID_CODE', Custom_Controller_Plugin_Guid::getGuid());
        StElog::init(APPLOG_PATH);
        StElog::pushNotice('datetime', date("Y-m-d H:i:s"));
        StElog::pushNotice('env', APPLICATION_ENV);
        StElog::pushNotice('uip', Custom_Controller_Plugin_Ipaddress::getIP());
        StElog::pushNotice('guid', GUID_CODE);
        StElog::pushNotice('uid', Tools::SetUid());
        StElog::pushNotice('url', $_SERVER["REQUEST_URI"]);
        StElog::pushNotice('refer', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
        StElog::pushNotice('agent', $_SERVER['HTTP_USER_AGENT']);
        StElog::pushNotice('system', 'manage');
    }

    public function _initRPCLog() {
        
    }

    protected function _initDLog() {
        $date = date("Y-m-d", time());
        if (in_array(APPLICATION_ENV, array("development", "testing"))) {
            $logger = Zend_Log::factory(array(
                        'timestampFormat' => 'Y-m-d H:i:s',
                        array(
                            'writerName' => 'Stream',
                            'writerParams' => array(
                                'stream' => APPLOG_PATH . DIRECTORY_SEPARATOR . "manageerror-{$date}.log",
                            ),
                            'formatterName' => 'Simple',
                            'formatterParams' => array(
                                'format' => '%timestamp% %message% %info%' . "\r\n",
                            ),
                            'filterName' => 'Priority',
                            'filterParams' => array(
                                'priority' => Zend_Log::WARN,
                            ),
                        ),
                        array(
                            'writerName' => 'Firebug',
                            'filterName' => 'Priority',
                            'filterParams' => array(
                                'priority' => Zend_Log::INFO,
                            ),
                        ),
            ));
        } else {
            $logger = Zend_Log::factory(array(
                        'timestampFormat' => 'Y-m-d H:i:s',
                        array(
                            'writerName' => 'Stream',
                            'writerParams' => array(
                                'stream' => APPLOG_PATH . DIRECTORY_SEPARATOR . "manageerror-{$date}.log",
                            ),
                            'formatterName' => 'Simple',
                            'formatterParams' => array(
                                'format' => '%timestamp% %message% %info%' . "\r\n",
                            ),
                            'filterName' => 'Priority',
                            'filterParams' => array(
                                'priority' => Zend_Log::WARN,
                            ),
                        )
            ));
        }
        Zend_Registry::set("ELog", $logger);
    }

}
