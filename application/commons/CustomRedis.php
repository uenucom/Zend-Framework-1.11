<?php
class CustomRedis {

    private static $_instance;
    private static $self;
    public static function getInstance($opt) {
        if (!(self::$_instance)) {
            try {
                $redis = new Redis();
                $redis->connect($opt['host'], $opt['port']);
                $redis->select(1);
                self::$_instance = $redis;
                self::$self = new self();
            } catch (Exception $e) {

            }
        }
        return self::$_instance;
    }

    function __destruct() {
        if (self::$_instance) {
            self::$_instance->close();
        }
    }
}
//$this->redis = Zend_Registry::get('redis');