<?php

class Custom_Controller_Plugin_Guid {

    /**
     * 获取本地IP、主机名
     *
     * @return string
     */
    public static function getLocalhost() {
        return strtolower($_SERVER["USER"] . '/' . $_SERVER["SERVER_ADDR"]);
    }

    /**
     * 获取当前微秒数
     *
     * @return string
     */
    public static function currentTimeMillis() {
        list($usec, $sec) = explode(" ", microtime());
        return $sec . substr($usec, 2, 3);
    }

    /**
     * 获取随机数
     *
     * @return string
     */
    public static function nextLong() {
        $tmp = rand(0, 1) ? '-' : '';
        return $tmp . rand(1000, 9999) . rand(1000, 9999) . rand(1000, 9999) . rand(100, 999) . rand(100, 999);
    }

    /**
     * 获取GUID
     *
     * @return string
     */
    public static function getGuid() {
        $raw = strtoupper(md5(Custom_Controller_Plugin_Guid::getLocalHost() . ':' . Custom_Controller_Plugin_Guid::currentTimeMillis() . ':' . Custom_Controller_Plugin_Guid::nextLong()));
        return substr($raw, 0, 8) . '-' . substr($raw, 8, 4) . '-' . substr($raw, 12, 4) . '-' . substr($raw, 16, 4) . '-' . substr($raw, 20);
    }

}
