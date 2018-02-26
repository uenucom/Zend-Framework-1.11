<?php

include_once 'thrift/Thrift.php';
include_once 'thrift/transport/TSocket.php';
include_once 'thrift/protocol/TBinaryProtocol.php';
include_once 'thrift/transport/TFramedTransport.php';

include_once 'thrift/packages/tieba/TieBa.php';
include_once 'thrift/packages/tieba/tieba_types.php';
include_once 'ThriftFunction.php';

class Custom_ThriftTieBaRPC {

    private $socket;
    private $transport;
    private $protocol;
    private $proto;

    function __construct($RpcServer_Conf = array()) {
        $RpcServerConf = Thrift_Select_Server($RpcServer_Conf);
        $this->socket = new TSocket($RpcServerConf["ip"], $RpcServerConf["port"]);
        $this->transport = new TFramedTransport($this->socket, 1024, 1024);
        $this->protocol = new TBinaryProtocolAccelerated($this->transport);
        $this->socket->setSendTimeout($RpcServerConf["timeout"]);
        $this->socket->setRecvTimeout($RpcServerConf["timeout"]);
    }

    //获取用户昵称
    public function getNickName($userId) {
        $configArray = array(
            'action' => 'getNickName',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userId' => $userId,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    //更新用户昵称
    public function updateNickName($userId, $nickName) {
        $configArray = array(
            'action' => 'updateNickName',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userId' => $userId,
                'nickName' => $nickName,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    //上传头像
    public function updatePhoto($userId, $photoUrl) {
        $configArray = array(
            'action' => 'updatePhoto',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userId' => $userId,
                'photoUrl' => $photoUrl,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    //获得用户信息
    public function getUserExtendInfo($userId) {
        $configArray = array(
            'action' => 'getUserExtendInfo',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userId' => $userId,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    //批量获得用户信息
    public function getUserExtendInfos($userIds) {
        $configArray = array(
            'action' => 'getUserExtendInfos',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userIds' => $userIds,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }
    /**
     * 创建贴吧
     * @param string $tbName
     * @param int $userId
     * @param string $sourceId
     * @param type $type
     * @return type
     */
    public function createForumColumn($tbName, $userId, $sourceId, $type) {
        $configArray = array(
            'action' => 'createForumColumn',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'tbName' => (string) $tbName,
                'userId' => $userId,
                'sourceId' => (string) $sourceId,
                'type' => $type,
            ),
        );
        //print_r($configArray);exit();
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    /**
     * 根据吧ID获取帖子列表
     * @param int $userId
     * @param int $forumId
     * @param int $type
     * @param int $start
     * @param int $limit
     * @param int $version
     * @return array
     */
    public function getThreadListByForumId($userId, $forumId, $type, $start, $limit, $version) {
        $configArray = array(
            'action' => 'getThreadListByForumId',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userId' => $userId, //0表示游客
                'forumId' => $forumId,
                'type' => $type,
                'start' => $start,
                'limit' => $limit, //类型 0 所有帖子(排序时：置顶优先)  1 精品贴  2 投票帖
                'version' => $version, //0:默认版本 1:H5版本 2:PC版本
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    /**
     * 获取帖子明细及回复列表
     * @param int $userId
     * @param string $threadId
     * @param int $start
     * @param int $limit
     * @param int $sort
     * @param int $page
     * @param int $type
     * @param int $isHits
     * @return array
     */
    public function getThreadContent($userId, $threadId, $start, $limit, $sort, $page, $type, $isHits) {
        $configArray = array(
            'action' => 'getThreadContent',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userId' => $userId,
                'threadId' => (string) $threadId, //threadId 帖子Id
                'start' => $start,
                'limit' => $limit,
                'sort' => $sort, //sort为回复排序0为倒序，1为正序
                'page' => $page, //为主贴的页数
                'type' => $type, //表示主题帖的分页模式(1,----500字2，---1000字3，全部)
                'isHits' => $isHits, //isHits表示是否增加点击量
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    /**
     * 发帖
     * @param int $userId
     * @param int $forumId
     * @param string $title
     * @param string $content
     * @param int $isUp
     * @param int $isVote
     * @return array
     */
    public function newPostOperation($userId, $forumId, $title, $content, $isUp, $isVote) {
        $configArray = array(
            'action' => 'newPostOperation',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userId' => $userId,
                'forumId' => $forumId, //threadId 帖子Id
                'title' => (string) $title,
                'content' => (string) $content,
                'isUp' => $isUp,
                'isVote' => $isVote,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    /**
     * 回帖
     * @param int $userId
     * @param string $threadId
     * @param int $floorId
     * @param string $content
     * @return array
     */
    public function threadReply($userId, $threadId, $floorId, $content) {
        $configArray = array(
            'action' => 'threadReply',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userId' => $userId,
                'threadId' => (string) $threadId, //threadId 帖子Id
                'floorId' => $floorId,
                'content' => (string) $content,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    //对帖子进行各种操作[置顶/精华/锁帖/撤销/置顶/解锁] 
    //threadOperation(1:i32 adminId,2:i32 forumId,3:i64 threadId,4:i32 type);
    public function threadOperation($adminId, $forumId, $threadId, $type) {
        $configArray = array(
            'action' => 'threadOperation',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'adminId' => $adminId,
                'forumId' => $forumId, //threadId 帖子Id
                'threadId' => $threadId,
                'type' => $type, //type  1：置顶  2：精华   3：锁帖  4：撤销置顶 5：解锁 6：取消精华
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    //获取getProductInfo

    public function getProductInfo($sourceId, $type, $replys) {
        $configArray = array(
            'action' => 'getProductInfo',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'sourceId' => $sourceId,
                'type' => $type,
                'replys' => $replys,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    //
    public function getSourceCommentInfo($sourceId, $type) {
        $configArray = array(
            'action' => 'getSourceCommentInfo',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'sourceId' => $sourceId,
                'type' => $type,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    //获取barid
    public function getTiebaStateResult($sourceId, $type) {
        $configArray = array(
            'action' => 'getSourceCommentInfo',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'sourceId' => $sourceId,
                'type' => $type,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    //获取吧ID的哪个接口
    public function getForumIdBySourceId($sourceId, $type) {
        $configArray = array(
            'action' => 'getForumIdBySourceId',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'sourceId' => $sourceId,
                'type' => $type,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    public function checkNickName($nickName) {
        $configArray = array(
            'action' => 'checkNickName',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'nickName' => $nickName,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    /**
     * 删除话题
     * @param int $userId
     * @param int $threadId
     * @param int $forumId
     * @return array
     */
    public function deleteThread($userId, $threadId, $forumId) {
        $configArray = array(
            'action' => 'deleteThread',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userId' => $userId,
                'threadId' => $threadId,
                'forumId' => $forumId,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    /**
     * 删除帖子回复
     * @param int $userId
     * @param int $forumId
     * @param int $threadId
     * @param int $replyId
     * @return boolean
     */
    public function deleteReply($userId, $forumId, $threadId, $replyId) {
        $configArray = array(
            'action' => 'deleteReply',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'userId' => $userId,
                'forumId' => $forumId,
                'threadId' => $threadId,
                'replyId' => $replyId,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    /**
     * 获取贴吧帖子总数、 回复总数
     * @param int $forumClassId
     * @return array
     */
    public function getForumClassHasNode($forumClassId) {
        $configArray = array(
            'action' => 'getForumClassHasNode',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'forumClassId' => $forumClassId,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

    /**
     * 获取贴吧管理员列表
     * @param int $forumId
     * @return list
     */
    public function getForumAdmins($forumId) {
        $configArray = array(
            'action' => 'getForumAdmins',
            "class_name" => "thrift_TieBaClient",
            "ext_key" => "",
            'param' => array(
                'forumId' => $forumId,
            ),
        );
        return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
    }

}