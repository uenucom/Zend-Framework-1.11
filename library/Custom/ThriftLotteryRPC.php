<?php
include_once 'thrift/Thrift.php';
include_once 'thrift/transport/TSocket.php';
include_once 'thrift/protocol/TBinaryProtocol.php';
include_once 'thrift/transport/TFramedTransport.php';

include_once 'thrift/packages/lottery/CrmLottery.php';
include_once 'thrift/packages/lottery/lottery_types.php';
include_once 'ThriftFunction.php';

class Custom_ThriftLotteryRPC {

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


	//创建抽奖

	function enteringLottery($req){
		$configArray = $this->configArray($req, 'enteringLottery', 'CrmLotteryClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}

	//删除抽奖

	function deleteLotteryInfo($req){
		$configArray = $this->configArray($req, 'deleteLotteryInfo', 'CrmLotteryClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	//使抽奖失效

	function setLotteryExpire($req){
		$configArray = $this->configArray($req, 'setLotteryExpire', 'CrmLotteryClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	//获得所有抽奖活动列表接口

	function getAllLottery($req){
		$configArray = $this->configArray($req, 'getAllLottery', 'CrmLotteryClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}


	//获得抽奖的结果接口  

	function getLotteryResult($req){
		$configArray = $this->configArray($req, 'getLotteryResult', 'CrmLotteryClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	//获得抽奖的列表接口 
	function getLotteryList($req){
		$configArray = $this->configArray($req, 'getLotteryList', 'CrmLotteryClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	//获取抽奖活动的信息，包括抽奖基本信息以及奖品列表
	function getLotteryInfo($req){
		$configArray = $this->configArray($req, 'getLotteryInfo', 'CrmLotteryClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	//录入抽奖活动接口
	function enteringLotteryNew($req){
		$configArray = $this->configArray($req, 'enteringLotteryNew', 'CrmLotteryClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
}