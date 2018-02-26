<?php
//10.23.245.106   ----  test
$rpcArrConf = array(
	"Server" => array (
		"type" => McpackRpc::MCPACK_RPC_CALL,
		'interface' => array(
			'sort' => array(
				'proto' => 'McpackRpcProto',
			),
		),
		"talk" => array(
			'connect_timeout_ms' => 100,
			'read_timeout_ms' => 3000,
			'write_timeout_ms' => 3000,
			'retry' => 3,
		),
		'machine' => array(
			array (
				'host' =>  '192.168.1.217', //'10.10.1.29',
				'port' =>  61006, //端口不确定暂为61006
			),
		),
	),
	"Server1" => array (
		"type" => McpackRpc::MCPACK_RPC_CALL,
		'interface' => array(
			'sort' => array(
				'proto' => 'McpackRpcProto',
			),
		),
		"talk" => array(
			'connect_timeout_ms' => 100,
			'read_timeout_ms' => 3000,
			'write_timeout_ms' => 3000,
			'retry' => 3,
		),
		'machine' => array(
			array (
				'host' => '192.168.1.223',//'10.10.1.45',
				'port' =>  61008, //61006
			),
			array (
				'host' => '192.168.1.221', //10.10.1.34
				'port' =>  61008,
			),
			array (
				'host' => '192.168.1.219', //'10.10.1.34',
				'port' =>  61008,
			),
			array (
				'host' => '192.168.1.217', //10.10.1.29
				'port' =>  61008,
			),
		),
	),
);
