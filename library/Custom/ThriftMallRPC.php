<?php
include_once 'thrift/Thrift.php';
include_once 'thrift/transport/TSocket.php';
include_once 'thrift/protocol/TBinaryProtocol.php';
include_once 'thrift/transport/TFramedTransport.php';

include_once 'thrift/packages/mall/MallService.php';
include_once 'thrift/packages/mall/mall_types.php';
include_once 'ThriftFunction.php';

class Custom_ThriftMallRPC {

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
	
	//添加商品 
	public function addMall($req) {
		$configArray = array(
            'action' => 'addCommodity',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	//更改商品
	public function changeMall($req) {
		$configArray = array(
            'action' => 'changeCommodity',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	//代金卷入库   
	public function addVoucherStore($req) {
		$configArray = array(
            'action' => 'addVoucherStore',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	//充值卡入库   
	public function addChargeCard($req) {
		$configArray = array(
            'action' => 'addChargeCardStore',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	//批量充值卡入库   
	public function batchAddChargeCardStore($req) {
		$configArray = array(
            'action' => 'batchAddChargeCardStore',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	//抽奖卡入库   
	public function addLotteryCardStore($req) {
		$configArray = array(
            'action' => 'addLotteryCardStore',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	//礼包入库   
	public function addPackStore($req) {
		$configArray = array(
            'action' => 'addPackStore',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	//更改商品上下架状态   
	public function changeCommodityShelfStatus($req) {
		$configArray = array(
            'action' => 'changeCommodityShelfStatus',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	//删除商品 
	public function removeCommodity($req) {
		$configArray = array(
            'action' => 'removeCommodity',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}

	//获取商品列表
	public function getMalllist($req) {
		$configArray = array(
            'action' => 'getCommodityListFromDB',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	

	//获取商品列表
	public function getMallInfo($req) {
		$configArray = array(
            'action' => 'getCommodityInfoFromDB',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	
	//获取代金卷列表  for php
	public function getVoucherListFromDB($req) {
		$configArray = array(
            'action' => 'getVoucherListFromDB',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	//新的充值卡入库接口
	public function addChargeCardNew($req) {
		$configArray = array(
            'action' => 'addChargeCardNew',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	//充值卡卡号密码入库
	public function addChargeCardCode($req) {
		$configArray = array(
            'action' => 'addChargeCardCode',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	//获取充值卡列表  for php
	public function getChargeCardListFromDB($req) {
		$configArray = array(
            'action' => 'getChargeCardListFromDB',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	//校验道具id for php
	public function checkPropValid($req) {
		$configArray = array(
            'action' => 'checkPropValid',
            "class_name" => "MallServiceClient",
            "ext_key" => "",
            'param' => array(
                'req'=>$req
		),
		);
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
}