<?php
include_once 'thrift/Thrift.php';
include_once 'thrift/transport/TSocket.php';
include_once 'thrift/protocol/TBinaryProtocol.php';
include_once 'thrift/transport/TFramedTransport.php';

include_once 'thrift/packages/notifyProxyService/NotifyProxyThriftService.php';
include_once 'thrift/packages/notifyProxyService/notifyProxyService_types.php';
include_once 'ThriftFunction.php';

class Custom_ThriftXiaoxiRPC {

	private $socket;
	private $transport;
	private $protocol;
	private $proto;

	function __construct($RpcServer_Conf = array()) {
		$RpcServerConf = Thrift_Select_Server($RpcServer_Conf);
		$this->socket = new TSocket($RpcServerConf["ip"],$RpcServerConf["port"]);
		$this->transport = new TFramedTransport($this->socket, 1024, 1024);
		$this->protocol = new TBinaryProtocolAccelerated($this->transport);
		$this->socket->setSendTimeout($RpcServerConf["timeout"]);
		$this->socket->setRecvTimeout($RpcServerConf["timeout"]);
	}

	//获取系统消息总数
	public function getSystemNAllCountByType($userId,$bussiness) {
		$configArray = array(
				'action' => 'getSystemNAllCountByType',
				"class_name" => "NotifyProxyThriftServiceClient",
				"ext_key"	=> "",
				'param' => array(
						'uid' =>$userId,
			            'bussiness' => $bussiness,
				),
		);
		
		return ThriftDataModel::getData($this->transport,$this->protocol,$this->proto,$configArray);
		
	}
	//获取系统消息
	public function getSystemNListByType($userId,$bussiness,$start,$pageSize) {
		$configArray = array(
				'action' => 'getSystemNListByType',
				"class_name" => "NotifyProxyThriftServiceClient",
				"ext_key"	=> "",
				'param' => array(
						'uid' =>$userId,
			            'bussiness' => $bussiness,
			            'start' => $start,
			            'pageSize' => $pageSize,
				),
		);
		return ThriftDataModel::getData($this->transport,$this->protocol,$this->proto,$configArray);
	}
	//获取系统消息

	public function getSystemNWithBussByType($userId,$bussiness,$snId) {
		$configArray = array(
				'action' => 'getSystemNWithBussByType',
				"class_name" => "NotifyProxyThriftServiceClient",
				"ext_key"	=> "",
				'param' => array(
						'uid' =>$userId,
						'bussiness' => $bussiness,
						'snId' => $snId,
	
				),
		);
		return ThriftDataModel::getData($this->transport,$this->protocol,$this->proto,$configArray);
	}
	//标记系统消息
	public function modifySystemNotifyToReadByType($userId,$bussiness,$snId) {
		$configArray = array(
				'action' => 'modifySystemNotifyToReadByType',
				"class_name" => "NotifyProxyThriftServiceClient",
				"ext_key"	=> "",
				'param' => array(
						'uid' =>$userId,
						'bussiness' => $bussiness,
						'snId' => $snId,
	
				),
		);
		return ThriftDataModel::getData($this->transport,$this->protocol,$this->proto,$configArray);
	}
	//删除系统消息
	public function deleteSystemListByType($userId,$bussiness,$snIdList) {
		$configArray = array(
				'action' => 'deleteSystemListByType',
				"class_name" => "NotifyProxyThriftServiceClient",
				"ext_key"	=> "",
				'param' => array(
						'uid' =>$userId,
						'bussinessType' => $bussiness,
						'snIdList' => $snIdList,
	
				),
		);
		
		return ThriftDataModel::getData($this->transport,$this->protocol,$this->proto,$configArray);
	}
	
}