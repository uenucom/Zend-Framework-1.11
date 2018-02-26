<?php
include_once 'thrift/Thrift.php';
include_once 'thrift/transport/TSocket.php';
include_once 'thrift/protocol/TBinaryProtocol.php';
include_once 'thrift/transport/TFramedTransport.php';

include_once 'thrift/packages/user/UserService.php';
include_once 'thrift/packages/user/user_types.php';
include_once 'ThriftFunction.php';

class Custom_ThriftUserRPC {

	private $socket;
	private $transport;
	private $protocol;
	private $proto;

	function __construct($RpcServer_Conf = array()) {
		$RpcServerConf = Thrift_Select_Server($RpcServer_Conf);
		$this->socket = new TSocket($RpcServerConf["ip"],$RpcServerConf["port"]);
		$this->transport = new TFramedTransport($this->socket, 1024, 1024);
		$this->protocol  = new TBinaryProtocolAccelerated($this->transport);
		$this->socket->setSendTimeout($RpcServerConf["timeout"]);
		$this->socket->setRecvTimeout($RpcServerConf["timeout"]);
	}


	private  function configArray($req,$action,$classname){
		$configArray = array(
            'action' => $action,
            "class_name" => $classname,
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return  $configArray;
	}
	
	//积分项列表  
	
	function getScoreItems($req){
		$configArray = $this->configArray($req, 'getScoreItems', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);		
	}

	
	//设置积分项
	
	function setintegralterm($req){
		$configArray = $this->configArray($req, 'setScoreItem', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);		
	}

    //新建VIP等级  
	function  createVip($req){
		$configArray = $this->configArray($req, 'setVip', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //VIP等级列表  
	function  getVips($req){
		$configArray = $this->configArray($req, 'getVips', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	
    //VIP等级列表  
	function  getAllVips($req){
		$configArray = $this->configArray($req, 'getAllVips', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //通过id获取单个积分项
	function  getScoreItem($req){
		$configArray = $this->configArray($req, 'getScoreItem', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //通过id获取单个vip等级设置 
	function  getVip($req){
		$configArray = $this->configArray($req, 'getVip', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //星玩家等级管理 添加
	function  addNewEmpiricDegree($req){
		$configArray = $this->configArray($req, 'addNewEmpiricDegree', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //星玩家等级管理 编辑 
	function  updateEmpiricDegree($req){
		$configArray = $this->configArray($req, 'updateEmpiricDegree', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //vip权益文本设置
	function  updateVipDetail($req){
		$configArray = $this->configArray($req, 'updateVipDetail', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
    //获取所有经验等级
	function  getAllEmpiricDegree($req){
		$configArray = $this->configArray($req, 'getAllEmpiricDegree', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
    //通过id经验等级
	function  getEmpiricDegreeById($req){
		$configArray = $this->configArray($req, 'getEmpiricDegreeById', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //获取vip权益详情
	function  getVipDetail($req){
		$configArray = $this->configArray($req, 'getVipDetail', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
    //获取当前可用的下一个经验等级id
	function  getNextEmpiricDegree($req){
		$configArray = $this->configArray($req, 'getNextEmpiricDegree', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //新版获取积分项列表接口
	function  getAllScoreItemList($req){
		$configArray = $this->configArray($req, 'getAllScoreItemList', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //点击连续登录详情时候的接口
	function  getContinueLoginTypeDetail($req){
		$configArray = $this->configArray($req, 'getContinueLoginTypeDetail', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
    //点击普通积分项详情(非连续登录)时候的接口
	function  getCommonTypeDetail($req){
		$configArray = $this->configArray($req, 'getCommonTypeDetail', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //新版连续登录修改设置接口,即 保存按钮时候的接口
	function  modifyContinueLogin($req){
		$configArray = $this->configArray($req, 'modifyContinueLogin', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
    //新版普通积分项修改设置接口
	function  modifyCommonType($req){
		$configArray = $this->configArray($req, 'modifyCommonType', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //通过连续登录积分项名称获取vip分值详情信息
	function  getCLVipScoreDetail($req){
		$configArray = $this->configArray($req, 'getCLVipScoreDetail', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
    //获取最新的连续登录的下一个天数
	function  getNextCLDayNumber($req){
		$configArray = $this->configArray($req, 'getNextCLDayNumber', 'UserServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
}