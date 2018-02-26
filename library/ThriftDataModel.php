<?php

class ThriftDataModel {

	static $redisClient = null;

	public static function getRedisClient() {
		if (!ThriftDataModel::$redisClient) {
			ThriftDataModel::$redisClient = new YCRedisClient($GLOBALS['redisInterfaceCache']);
		}
		return ThriftDataModel::$redisClient;
	}

	public static function getData($transport, $protocol, $proxy, $RpcSockArr) {
		/**
		 $configArray=array();
		 if( $configArray['cache']['key'] && $configArray['cache']['time'] ){
		 $cacheResult = ThriftDataModel::getRedisClient()->getInterfaceCache($configArray['cache']['key']);
		 if( !empty($cacheResult) ){
		 return $cacheResult;
		 }
		 }
		 * */

		$result = ThriftDataModel::getDataFromThrift($transport, $protocol, $proxy, $RpcSockArr);
		/**
		 if( !empty($result) ){
		 if( $configArray['cache']['key'] && $configArray['cache']['time'] ){
		 ThriftDataModel::getRedisClient()->saveInterfaceCahce($configArray['cache']['key'], $result,$configArray['cache']['time']);
		 }
		 }
		 * */
			
		return $result;
	}

	public static function getmicrotime() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float) $usec + (float) $sec);
	}

	public static function array_is_null($arr = null) {
		if (is_array($arr)) {
			foreach ($arr as $k => $v) {
				if ($v && !is_array($v)) {
					return false;
				}
				$t = ThriftDataModel::array_is_null($v);
				if (!$t) {
					return false;
				}
			}
			return true;
		} elseif (!$arr) {
			return true;
		} else {
			return false;
		}
	}

	public static function getDataFromThrift($transport, $protocol, $proxy, $RpcSockArr) {

		$start_call_rpc = ThriftDataModel::getmicrotime();
		//参数列表
		$params = array();
		$func_name = $RpcSockArr['action'];
		$class_name = $RpcSockArr['class_name'];

		$client = new $class_name($protocol);
		//反射机制拿函数定义
		$reflector = new ReflectionClass($class_name);
		$RMethod = $reflector->getMethod($func_name);
		$MethodParams = $RMethod->getParameters();
		$len = count($MethodParams);

		for ($i = 0; $i < $len; $i++) {
			$parameter = $MethodParams[$i];
			$pname = $parameter->getName();
			$ppos = $parameter->getPosition();
			$params[$ppos] = $RpcSockArr["param"][$pname];
		}
		$errorMessage = "";
		try {

			if (!$transport->isOpen()) {
				$transport->open();
			}

			$output = call_user_func_array(array($client, $func_name), $params);

			$transport->close();
		} catch (Exception $tx) {
			$errorMessage = $tx->getMessage();
			if ($transport->isOpen()) {
				$transport->close();
			}
		}

		if (is_array($RpcSockArr["param"])) {

			foreach ($RpcSockArr["param"] as $key => $val) {
				if(is_object($val)){
					$paramStr = "(";
					foreach ($val as $keyone => $valueone) {
						if(!is_array($valueone)and!is_object($valueone)){
							$valueone  = ($valueone);
							$paramStr .= $keyone.':'.$valueone.',';
						} else {

							if(count($valueone)>1){//大于一个数组

								$paramStr .= $keyone.':'."(";
								foreach ($valueone as $keytwo=> $valuetwo) {

									if(is_numeric($keytwo)){

										if(is_object($valuetwo)){
											$paramStr.="(";
											foreach ($valuetwo as $keythree => $valuethree) {
												if(!is_object($valuethree)){
													$valuethree = ($valuethree);
													$paramStr .= $keythree.':'.$valuethree.',';
												}else {


													$paramStr .= $keythree.':'.'(';
													foreach ($valuethree as $keyfour => $valuefour) {
														if(!is_array($valuefour)){
															$valuefour = ($valuefour);
															$paramStr.= $keyfour.':'.$valuefour.',';
														}else{
															$paramStr .= $keyfour.':'.'(';
															foreach ($valuefour as $keyfive => $valuefive) {
																if(is_numeric($keyfive)){
																	$valuefive = ($valuefive);
																	$paramStr.= $valuefive.',';
																}
															}
															$paramStr .= '),';
														}
													}
													$paramStr.='),';


												}
											}
											$paramStr.="),";
										} else {
											$paramStr .=''.$valuetwo.',';
										}

									}
								}
								//oneforeach end
								$paramStr.="),";
							} else {

								//two start
								$paramStr.=$keyone.':'.'(';
								foreach ($valueone as $keytwo=> $valuetwo) {
									if(!is_numeric($keytwo)){
										if(is_object($valuetwo)){

											$paramStr.= $keytwo.':'."(";
											foreach ($valuetwo as $keythree => $valuethree) {
												if(!is_array($valuethree)){
													$valuethree = ($valuethree);
													$paramStr .= $keythree.':'.$valuethree.',';
												}else {


													$paramStr .= $keythree.':'.'(';
													foreach ($valuethree as $keyfour => $valuefour) {
														if(!is_array($valuefour)){
															if(!is_numeric($keyfour)){
																$valuefour = ($valuefour);
																$paramStr.= $keyfour.':'.$valuefour.',';
															} else {
																$valuefour = ($valuefour);
																$paramStr.=  $valuefour.',';
															}
														}else{
															foreach ($valuefour as $keyfive => $valuefive) {
																$paramStr .= $keyfour.':'.'(';
																if(is_numeric($keyfive)){
																	$valuefive = ($valuefive);
																	$paramStr.= $valuefive.',';
																}
																$paramStr .= ')';
															}
														}
													}
													$paramStr.='),';


												}
											}
											$paramStr.="),";



										} else {

											$valuetwo = ($valuetwo);
											$paramStr.= $keytwo.':'.$valuetwo.',';//修改的值
										}

									} else {
										if(is_object($valuetwo)){

											$paramStr.=  "(";
											foreach ($valuetwo as $keythree => $valuethree) {
												if(!is_array($valuethree)and!is_object($valuethree)){
													$valuethree = ($valuethree);
													$paramStr .= $keythree.':'.$valuethree.',';
												}else {


													$paramStr .= $keythree.':'.'(';
													foreach ($valuethree as $keyfour => $valuefour) {
														if(!is_array($valuefour)){
															if(!is_numeric($keyfour)){
																$valuefour = ($valuefour);
																$paramStr.= $keyfour.':'.$valuefour.',';
															} else {
																$valuefour = ($valuefour);
																$paramStr.=  $valuefour.',';
															}
														}else{
															foreach ($valuefour as $keyfive => $valuefive) {
																$paramStr .= $keyfour.':'.'(';
																if(is_numeric($keyfive)){
																	$valuefive = ($valuefive);
																	$paramStr.= $valuefive.',';
																}
																$paramStr .= ')';
															}
														}
													}
													$paramStr.='),';
												}
											}
											$paramStr.="),";
										} else {
											if(is_numeric($keytwo)){
												$valuetwo = ($valuetwo);
												$paramStr.=$valuetwo.',';//修改的值
											} else {
												$valuetwo = ($valuetwo);
												$paramStr.= $keytwo.':'.$valuetwo.',';//修改的值
											}
										}
									}
								}
								$paramStr.='),';
								//two end
							}

						}

					}
					$paramStr .= ")";

				}
			}

		}
		$file = fopen('/home/work/www/front/logs/'.date('Y-m-d').'---crm---manage.log', "ab+");
		//$file = fopen('../0716manage.txt', "ab+");
		fwrite($file, date("Ymdhis").substr($paramStr,0,1024).""."\r\n");
		fclose($file);
		//echo $paramStr;
		if ($errorMessage) {
			$pageid = $_SERVER["REQUEST_URI"];
			echo $errorMessage;
			Bingo_Log::warning('RPC_Error:SNS ' . $RpcSockArr["action"] . "[" . $errorMessage . "]" . $paramStr . "@" . $pageid, LOG_DAL);
		} else {
			if (isset($GLOBALS["RPC_Return"])) {
				$GLOBALS["RPC_Return"] .= "action:" . $RpcSockArr["action"] . $paramStr;
			} else {
				$GLOBALS["RPC_Return"] = "action:" . $RpcSockArr["action"] . $paramStr;
			}
			$end_call_rpc = ThriftDataModel::getmicrotime();
			$period = $end_call_rpc - $start_call_rpc;
			//是否打印接口返回参数
			if (true) {

				$RPC_TIME_LOG_STR = $RpcSockArr["action"] . " " . $paramStr . " COST_TIME[" . $period . "]";
				if (ThriftDataModel::array_is_null($output)) {
					$RPC_TIME_LOG_STR .= " ArrayIsNull[" . json_encode($output) . "]";
				} else {
					//                    $RPC_TIME_LOG_STR .= " ResultArray[".json_encode($output)."]";
				}
				//Bingo_Log::warning("SNS ".$RPC_TIME_LOG_STR,LOG_DAL);
			}
		}
		return @$output;
	}

}
