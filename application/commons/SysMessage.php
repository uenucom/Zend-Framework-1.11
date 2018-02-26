<?php

class SysMessage {

    /**
     * 
     * @param type $msg
     * @param type $status
     * @return type
     */
    public static function info($status = 'ok', $msg = '') {
        $msginfo = array();
        if ($status == 'ok' || $msg['statusCode'] == 200) {
            $msginfo['statusCode'] = 200;
            $msginfo['message'] = "操作成功";
            $msginfo['rel'] = '';
            $msginfo['callbackType'] = 'closeCurrent';
            $msginfo['forwardUrl'] = '';
            $msginfo['confirmMsg'] = '';
        } else {
            $msginfo['statusCode'] = 300;
            $msginfo['message'] = "操作成功";
            $msginfo['rel'] = '';
            $msginfo['callbackType'] = '';
            $msginfo['forwardUrl'] = '';
            $msginfo['confirmMsg'] = '';
        }
        if (is_array($msg) && count($msg) > 0) {
            foreach ($msg as $key => $val) {
                $msginfo[$key] = $val;
            }
        }
        return $msginfo;
    }

    public static function setMessage($status = 'ok') {
        $msginfo = array();
        if ($status == 'ok') {
            $msginfo['statusCode'] = 200; 
            $msginfo['message'] = "操作成功";
            $msginfo['rel'] = '';
            $msginfo['callbackType'] = 'closeCurrent';
            $msginfo['forwardUrl'] = '';
            $msginfo['confirmMsg'] = '';
        } else {
            $msginfo['statusCode'] = 300;
            $msginfo['message'] = "操作成功";
            $msginfo['rel'] = '';
            $msginfo['callbackType'] = '';
            $msginfo['forwardUrl'] = '';
            $msginfo['confirmMsg'] = '';
        }
        return $msginfo;
    }

}
