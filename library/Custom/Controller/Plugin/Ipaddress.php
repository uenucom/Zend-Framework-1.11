<?php


/**
 * 取IP地址
 *
 */
class Custom_Controller_Plugin_Ipaddress {

    function __construct() {
        
    }

    public static function getIP() {
        if (!empty($_SERVER ["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER ["HTTP_CLIENT_IP"];
        } else if (!empty($_SERVER ["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
        } else if (!empty($_SERVER ["REMOTE_ADDR"])) {
            $cip = $_SERVER ["REMOTE_ADDR"];
        } else {
            $cip = "";
        }
        return $cip;
    }

}
