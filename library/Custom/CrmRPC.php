<?php

include_once "mcpackrpc/rpc.php";

class Custom_CrmRPC {

    private $proto;

    function __construct($RpcServerConf = array()) {
        $this->proto = new McpackRpc($RpcServerConf);
    }

    //获取自定义书籍列表
    public function get_check_gid_in_db($gid) {
        $configArray = array(
            'method' => 'check_gid_in_db',
            'param' => array(
                'gid' => (string)$gid,
            ),
//            'cache' => array(
//                'key' => '',
//                'time' => 900,
//            ),
        );
        //$configArray['cache']['key'] = "get_apk_book_topic_list_" . $configArray['param']['topic_id'] . $configArray['param']['page_num'] . $configArray['param']['page_size'];
        return RPCDataModel::getData($this->proto, $configArray);
    }

}