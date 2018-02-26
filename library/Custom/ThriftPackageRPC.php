<?php
include_once 'thrift/Thrift.php';
include_once 'thrift/transport/TSocket.php';
include_once 'thrift/protocol/TBinaryProtocol.php';
include_once 'thrift/transport/TFramedTransport.php';

include_once 'thrift/packages/package/PackageThriftService.php';
include_once 'thrift/packages/package/package_types.php';
include_once 'ThriftFunction.php';

class Custom_ThriftPackageRPC {

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

	//创建消息

	function createMessage($req){
		$configArray = $this->configArray($req, 'InsActivity', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}


	//创建礼包

	function insPackageInfo($req){
		$configArray = $this->configArray($req, 'insPackageInfo', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}



	//创建礼包 条件

	function insPackageCond($req){
		$configArray = $this->configArray($req, 'insPackageCond', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}


	//导入礼包库

	function insPackageDetail($req){
		$configArray = $this->configArray($req, 'insPackageDetail', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}


	//创建平台活动

	function InsActivityPlateformActivities($req){
		$configArray = $this->configArray($req, 'InsActivity', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}



	//编辑平台活动

	function UpdateActivityInfoPlateformActivities($req){
		$configArray = $this->configArray($req, 'UpdateActivityInfo', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	//平台活动 列表

	function getActivityListForPhpPlateformActivities($req){
		$configArray = $this->configArray($req, 'getActivityListForPhp', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}


	//礼包 列表

	function getPackageSimpleViewList($req){
		$configArray = $this->configArray($req, 'getPackageSimpleViewList', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}

	//根据礼包id 获取  礼包信息

	function getPackageView($req){
		$configArray = $this->configArray($req, 'getPackageView', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}


	//关闭平台活动

	function closeActivity($req){
		$configArray = $this->configArray($req, 'closeActivity', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	//删除平台活动

	function DelActivity($req){
		$configArray = $this->configArray($req, 'DelActivity', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}

	//礼包下架

	function removePackageInfo($req){
		$configArray = $this->configArray($req, 'removePackageInfo', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}


	//根据id获取活动数据

	function getActivityById($req){
		$configArray = $this->configArray($req, 'getActivityById', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}

	//根据id编辑活动数据

	function updateActivityInfo($req){
		$configArray = $this->configArray($req, 'updateActivityInfo', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}

	//根据id编辑礼包数据

	function updatePackageInfo($req){
		$configArray = $this->configArray($req, 'updatePackageInfo', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	//根据礼包id 获取  礼包信息

	function exportPackageNum($req){
		$configArray = $this->configArray($req, 'exportPackageNum', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	/**
	 * 插入抽奖活动
	 **/
	function  insertLotteryActivity($req){
		$configArray = $this->configArray($req, 'insertLotteryActivity', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	/**
	 * 根据抽奖ID获取抽奖的有效开始和结束时间
	 **/
	function  getLotteryActivityValidTimeByLotteryId($req){
		$configArray = $this->configArray($req, 'getLotteryActivityValidTimeByLotteryId', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	/**
	 * 补仓 
	 */
	function  reimportPackageDetail($req){
		$configArray = $this->configArray($req, 'reimportPackageDetail', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	/**
	 * 获取推荐礼包
	 */
	function  checkPackageValidation($req){
		$configArray = $this->configArray($req, 'checkPackageValidation', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	/**
	 * 插入累计返利规则
	 */
	function  insAccumulationRebateRule($req){
		$configArray = $this->configArray($req, 'insAccumulationRebateRule', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	
	/**
	 * 插入单笔返利规则
	 */
	function  insOnceRebateRule($req){
		$configArray = $this->configArray($req, 'insOnceRebateRule', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	/**
	 * 插入随机返利规则
	 */
	function  insRandomRebateRule($req){
		$configArray = $this->configArray($req, 'insRandomRebateRule', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
	
	/**
	 * 根据礼包ID获取礼包数量信息   
	 */
	function  getPackageCountResult($req){
		$configArray = $this->configArray($req, 'getPackageCountResult', 'PackageThriftServiceClient');
		return ThriftDataModel::getData($this->transport, $this->protocol, $this->proto, $configArray);
	}
}