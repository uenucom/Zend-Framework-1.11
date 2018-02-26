<?php
include_once 'thrift/Thrift.php';
include_once 'thrift/transport/TSocket.php';
include_once 'thrift/protocol/TBinaryProtocol.php';
include_once 'thrift/transport/TFramedTransport.php';

include_once 'thrift/packages/strategy/NotifyStrategy.php';
include_once 'thrift/packages/strategy/strategy_types.php';
include_once 'ThriftFunction.php';

class Custom_ThriftStrategyRPC {

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
	//发送消息
	function  pushMessages($req){
		$configArray = $this->configArray($req, 'pushMessages', 'NotifyStrategyClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	//得到所有的消息
	function  getAllMessages($req){
		$configArray = $this->configArray($req, 'getAllMessages', 'NotifyStrategyClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}

	//获得消息详情接口
	function  getMessageDetail($req){
		$configArray = $this->configArray($req, 'getMessageDetail', 'NotifyStrategyClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}

	//删除未开始的消息接口
	function  deleteMessage($req){
		$configArray = $this->configArray($req, 'deleteMessage', 'NotifyStrategyClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
}