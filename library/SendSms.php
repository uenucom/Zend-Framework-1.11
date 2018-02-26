<?php


include_once "mcpackrpc/rpc.php";

function sendSMS($phone,$content){

	$arrConf = array(
		"Server1" => array (
			"type" => McpackRpc::MCPACK_RPC_CALL,
			'interface' => array(
				'sort' => array(
					'proto' => 'McpackRpcProto',
				),
			),
			"talk" => array(
				'connect_timeout_ms' => 1,
				'read_timeout_ms' => 1000,
				'write_timeout_ms' => 1000,
				'retry' => 3,
			),
			'machine' => array(
		array (
					'host' => '10.10.0.200',
					'port' =>  61002,
				),
				array (
					'host' => '10.10.0.200',
					'port' => 61003,
				),
			
			),
 		), 
	);


	$proto = new McpackRpc($arrConf);

	$proto->rpcCall(
		'Server1',
		'send_sms',
		array(
			'phone' => $phone,
			'content' => $content,
		),
		$output
	);


	
  return $output;
}

// $phone = "18610807275";
// $content = "1111";
//sendSMS($phone,$content);

?>