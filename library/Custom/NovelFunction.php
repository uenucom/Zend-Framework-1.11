<?php

include_once "mcpackrpc/rpc.php";

class Custom_NovelFunction {

    private $proto;

    function __construct($RpcServerConf = array()) {
        $this->proto = new McpackRpc($RpcServerConf);
    }

    //获取自定义书籍列表
    public function get_apk_book_topic_list($topic_id, $page_num, $page_size) {
        $configArray = array(
            'method' => 'get_apk_book_topic_list',
            'param' => array(
                'topic_id' => $topic_id,
                'page_num' => $page_num,
                'page_size' => $page_size
            ),
            'cache' => array(
                'key' => '',
                'time' => 900,
            ),
        );
        $configArray['cache']['key'] = "get_apk_book_topic_list_" . $configArray['param']['topic_id'] . $configArray['param']['page_num'] . $configArray['param']['page_size'];
        return RPCDataModel::getData($this->proto, $configArray);
    }

    
    //获取榜单书籍列表
    public function get_apk_book_top_range_list($top_type, $date_type, $class_type, $page_num, $page_size) {
        $configArray = array(
            'method' => 'get_apk_book_top_range_list',
            'param' => array(
                'top_type' => $top_type,
                'date_type' => $date_type,
                'class_type' => $class_type,
                'page_num' => $page_num,
                'page_size' => $page_size,
            ),
            'cache' => array(
                'key' => '',
                'time' => 900,
            ),
        );
        $configArray['cache']['key'] = "get_apk_book_top_range_list_" 
                . $configArray['param']['top_type']. $configArray['param']['date_type']. $configArray['param']['class_type']
                . $configArray['param']['page_num']. $configArray['param']['page_size'] ;
        return RPCDataModel::getData($this->proto, $configArray);
    }

    //获取榜单书籍列表
    public function get_apk_book_list_by_type($book_type_id, $apk_channel_id, $apk_type, $page_num, $page_size) {
        $configArray = array(
            'method' => 'get_apk_book_list_by_type',
            'param' => array(
                'book_type_id' => $book_type_id,
                'apk_channel_id' => $apk_channel_id,
                'apk_type' => $apk_type,
                'page_num' => $page_num,
                'page_size' => $page_size,
            ),
            'cache' => array(
                'key' => '',
                'time' => 900,
            ),
        );
        $configArray['cache']['key'] = "get_apk_book_list_by_type_" . $configArray['param']['book_type_id']
                . $configArray['param']['apk_channel_id']
                . $configArray['param']['apk_type']
                . $configArray['param']['page_num']
                . $configArray['param']['page_size'];
        return RPCDataModel::getData($this->proto, $configArray);
    }

    //根据书籍ID和类型，获取推荐书籍
    public function get_apk_book_recommends($bookid, $type, $num, $tag) {
        $configArray = array(
            'method' => 'get_apk_book_recommends',
            'param' => array(
                'bookid' =>(string)$bookid,
                'type' => $type,
                'num' => $num,
                'tag' => $tag,
            ),
            'cache' => array(
                'key' => '',
                'time' => 900,
            ),
        );
        $configArray['cache']['key'] = "get_apk_book_recommends_" . $configArray['param']['bookid']. $configArray['param']['type']
                . $configArray['param']['num']
                . $configArray['param']['tag'];
        return RPCDataModel::getData($this->proto, $configArray);
    }

    
    //获取书籍详细信息，包含下载按键
    public function get_apk_book_detail_info($bookid, $apk_channel_id, $apk_type, $pagesizetype) {
        //echo $bookid, '==',$apk_channel_id,'==', $apk_type,'==', $pagesizetype;exit();
        //var_dump($pagesizetype);exit();
        $configArray = array(
            'method' => 'get_apk_book_detail_info',
            'param' => array(
                'book_id' => (string)$bookid,
                'apk_channel_id' => (string)$apk_channel_id,
                'apk_type' => $apk_type,
                'pagesizetype' => $pagesizetype,
            ),
            'cache' => array(
                'key' => '',
                'time' => 900,
            ),
        );
        $configArray['cache']['key'] = "get_apk_book_detail_info_" . $configArray['param']['book_id']
                . $configArray['param']['apk_channel_id']
                . $configArray['param']['apk_type']
                . $configArray['param']['pagesizetype'];
        return RPCDataModel::getData($this->proto, $configArray);
    }

    
    //搜索 获取书籍详细信息，包含下载按键
    public function get_apk_book_list_by_filter($book_filter, $apk_channel_id, $apk_type, $page_num, $page_size) {
        $configArray = array(
            'method' => 'get_apk_book_list_by_filter',
            'param' => array(
                'book_filter' => (string)$book_filter,
                'apk_channel_id' => (string)$apk_channel_id,
                'apk_type' => $apk_type,
                'page_num' => $page_num,
                'page_size' => $page_size,
            ),
            'cache' => array(
                'key' => '',
                'time' => 900,
            ),
        );
        $configArray['cache']['key'] = "get_apk_book_list_by_filter_" . $configArray['param']['book_filter']
                . $configArray['param']['apk_channel_id']
                . $configArray['param']['apk_type']
                . $configArray['param']['page_num']
                . $configArray['param']['page_size'];
        return RPCDataModel::getData($this->proto, $configArray);
    }


    /**
     * 获取榜单书籍列表，包含下载信息
     * @param int $top_type
     * @param int $date_type
     * @param int $class_type
     * @param string $apk_channel_id
     * @param int $apk_type
     * @param int $page_num
     * @param int $page_size
     * @return array
     */
    public function get_apk_book_top_range_dl_list($top_type, $date_type, $class_type, $apk_channel_id, $apk_type, $page_num, $page_size)
    {
        $configArray = array(
            'method' => 'get_apk_book_top_range_dl_list',
            'param' => array(
                'top_type' => $top_type,
                'date_type' => $date_type,
                'class_type' => $class_type,
                'apk_channel_id' => (string)$apk_channel_id,
                'apk_type' => $apk_type,
                'page_num' => $page_num,
                'page_size' => $page_size,
            ),
            'cache' => array(
                'key' => '',
                'time' => 900,
            ),
        );
        $configArray['cache']['key'] = "get_apk_book_top_range_dl_list_" 
                . $configArray['param']['top_type']. $configArray['param']['date_type']
                . $configArray['param']['class_type']. $configArray['param']['apk_channel_id']
                . $configArray['param']['apk_type']
                . $configArray['param']['page_num']. $configArray['param']['page_size'] ;
        return RPCDataModel::getData($this->proto, $configArray);
    }
}
