<?php

/**
 * DB connection
 */
class DB {

    /**
     * 获取配置参数
     * @param array $params
     * @return \Zend_Config
     */
    public static function getConfig($params) {
        $profiler = new Custom_Db_Profilerlog();
        $profiler->setEnabled(true);
        $configData = array(
            'instance' => $profiler
        );
        $config = new Zend_Config($configData);
        $params['profiler'] = $config;
        return $params;
    }

    /**
     * 创建db对象并注册
     * @param string $label
     * @param string $alias
     */
    public static function conn($label, $alias = '') {
        $registry = Zend_Registry::getInstance();
        $dbAdapter = Zend_Db::factory($registry['dbconfig']->$label->adapter, self::getConfig($registry['dbconfig']->$label->config->toArray()));
        Zend_Registry::set((trim($alias) !== '') ? $alias : $label, $dbAdapter);
    }

    //广告数据库 
    public static function connMobileassistant($label, $alias = '') {
        $registry = Zend_Registry::getInstance();
        $dbAdapter = Zend_Db::factory($registry['dbconfig']->$label->adapter, self::getConfig($registry['dbconfig']->$label->config->toArray()));
        Zend_Registry::set((trim($alias) !== '') ? $alias : $label, $dbAdapter);
    }

    //统计 数据库 
    public static function connStatices($label, $alias = '') {
        $registry = Zend_Registry::getInstance();
        $dbAdapter = Zend_Db::factory($registry['dbconfig']->$label->adapter, self::getConfig($registry['dbconfig']->$label->config->toArray()));
        Zend_Registry::set((trim($alias) !== '') ? $alias : $label, $dbAdapter);
    }

    //平台id
    public static function connPlateform($label, $alias = '') {
        $registry = Zend_Registry::getInstance();
        $dbAdapter = Zend_Db::factory($registry['dbconfig']->$label->adapter, self::getConfig($registry['dbconfig']->$label->config->toArray()));
        Zend_Registry::set((trim($alias) !== '') ? $alias : $label, $dbAdapter);
    }

    //资源 id 
    public static function connSource($label, $alias = '') { 
        $registry = Zend_Registry::getInstance();
        $dbAdapter = Zend_Db::factory($registry['dbconfig']->$label->adapter, self::getConfig($registry['dbconfig']->$label->config->toArray()));
        Zend_Registry::set((trim($alias) !== '') ? $alias : $label, $dbAdapter);
    }

}
