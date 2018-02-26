<?php

class StElog {

    /**
     * none
     * @var int
     */
    const LOG_NONE = 0x00;

    /**
     * Fatal
     * @var int
     */
    const LOG_FATAL = 0x01;

    /**
     * Warning 
     * @var int
     */
    const LOG_WARNING = 0x02;

    /**
     * notice
     * @var int
     */
    const LOG_NOTICE = 0x04;

    /**
     * trace
     * @var int
     */
    const LOG_TRACE = 0x08;

    /**
     * debug
     * @var int
     */
    const LOG_DEBUG = 0x10;

    /**
     * all
     * @var int
     */
    const LOG_ALL = 0xFF;

    /**
     * level to text
     * @var array
     */
    public static $arrLogNames = array(
        self::LOG_FATAL => 'FATAL',
        self::LOG_WARNING => 'WARNING',
        self::LOG_NOTICE => 'NOTICE',
        self::LOG_TRACE => 'TRACE',
        self::LOG_DEBUG => 'DEBUG',
    );
    protected static $_arrNoticeNodes = array();
    protected static $_filePath;

    public static function debug($arr_message = "", $filename = "/tmp/fydebugdev.log") {
        if (is_array($arr_message)) {
            $mes = implode("|", $arr_message);
        } else {
            $mes = $arr_message;
        }
        $res = preg_replace(array('/\n/', '/\r/', '/\t/'), array(' ', '', ''), $mes);
        error_log(date("Y-m-d H:i:s") . "|" . $person_logid . "|" . $res . "\n", 3, $filename);
    }

    public static function init($filename = "/tmp/") {
        if (!empty($filename)) {
            $date = date("Y-m-d", time());
            self::$_filePath = APPLOG_PATH . DIRECTORY_SEPARATOR . "managelog-{$date}.log";
        }
    }

    public static function getContents() {
        $out = ob_get_contents();
        return $out;
    }

    public static function doRequest($obj) {
        $log = array();
        if ($obj->isPost()) {
            $params = $obj->getPost();
            if (!empty($params)) {
                foreach ($params as $key => $val) {
                    $log[] = $key . ":" . $val;
                }
            }
        }
//        else {
//            $params = $obj;
//            print_r($params);
//            if (!empty($params)) {
//                foreach ($params as $key => $val) {
//                    $log[] = $key . ":" . $val;
//                }
//            }
//        }
        return implode("|", $log);
    }

    public static function pushNotice($strKey, $strValue) {
        self::$_arrNoticeNodes[strval($strKey)] = strval($strValue);
    }

    public static function buildNotice($strOtherLog = '') {
        $strLog = '';
        if (!empty(self::$_arrNoticeNodes)) {
            foreach (self::$_arrNoticeNodes as $strKey => $strValue) {
                $strLog .= $strKey . '[' . $strValue . '] ';
            }
        }
        $strLog .= $strOtherLog;
        error_log($strLog . "\n", 3, self::$_filePath);
    }

    public static function useTime() {
        $returntime = round((get_microtime() - ST_TIME) * 1000, 0) . ' ms';
        return $returntime;
    }

}
