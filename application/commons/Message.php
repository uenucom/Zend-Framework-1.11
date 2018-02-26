<?php

class Message {

    /**
     * 
     * @param type $msg
     * @param type $status
     * @return type
     */
    public static function info($status ='ok', $msg='') {
        $msginfo = array();
        if ($status == 'ok' || $msg['statusCode'] == 200) {
            $msginfo['statusCode'] = 200; //错误300 
            $msginfo['message'] = "操作成功";
            //$msg['navTabId'] = 'dlg_page10';
            //$msg['dialogId'] = 'channelrate_update_show';
            $msginfo['rel'] = '';
            $msginfo['callbackType'] = 'closeCurrent';
            $msginfo['forwardUrl'] = '';
            $msginfo['confirmMsg'] = '';
        } else {
            $msginfo['statusCode'] = 300; //错误300 
            $msginfo['message'] = "操作成功";
            //$msg['navTabId'] = 'dlg_page10';
            //$msg['dialogId'] = 'channelrate_update_show';
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

}
