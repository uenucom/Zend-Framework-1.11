<?php

class Custom_Controller_Plugin_SSDB implements Zend_Session_SaveHandler_Interface {

    /**
     * Redis 实例
     * @var Redis
     */
    protected $_redis;

    /**
     * Session 前缀
     */
    protected $_prefix;

    /**
     * 获取Redis实例
     * @return Redis
     */
    protected function _getRedis() {
        if (!$this->_redis) {
            include_once('SSDB.php');
            try {
//            foreach ($config as $value) {
//                $mc->addServer($value['ip'], $value['port']);
//            }
                //$ssdb = new SimpleSSDB('192.168.1.145', 8888);
//                $ssdb = new SimpleSSDB('192.168.1.145', 8888);
//                StElog::pushNotice('ssdb.gc_maxlifetime', (int) ini_get('session.gc_maxlifetime')); //APPLICATION_ENV
                $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/ssdb.ini', null, true);
                $config = $config->config->params->toArray();
                $ssdb = CustomSSDB::getInstance($config['SSDB']);
                //unset($config['SSDB']);
            } catch (SSDBException $e) {
                Tools::writeLog(__LINE__ . ' ' . $e->getMessage());
            }

            $this->_redis = $ssdb;
        }
        return $this->_redis;
    }

    /**
     * 返回session生存时间
     * @return int
     */
    protected function _getLifetime() {
        return (int) ini_get('session.gc_maxlifetime');
    }

    /**
     * Open Session - retrieve resources
     *
     * @param string $save_path
     * @param string $name
     */
    public function open($save_path, $name) {
        $this->_prefix = $name . ':';
        return true;
    }

    /**
     * Close Session - free resources
     *
     */
    public function close() {
        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     */
    public function read($id) {
        return $this->_getRedis()->get($this->_prefix . $id);
    }

    /**
     * Write Session - commit data to resource
     *
     * @param string $id
     * @param mixed $data
     */
    public function write($id, $data) {
        return $this->_getRedis()->setx($this->_prefix . $id, $data, $this->_getLifetime());
    }

    /**
     * Destroy Session - remove data from resource for
     * given session id
     *
     * @param string $id
     */
    public function destroy($id) {
        return (bool) $this->_getRedis()->del($this->_prefix . $id);
    }

    /**
     * Garbage Collection - remove old session data older
     * than $maxlifetime (in seconds)
     *
     * @param int $maxlifetime
     */
    public function gc($maxlifetime) {
        return true;
    }

}
