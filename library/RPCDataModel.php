<?php

include_once "mcpackrpc/rpc.php";

class RPCDataModel {

    static $redisClient = null;

    public static function getRedisClient() {
        if (!RPCDataModel::$redisClient) {
            RPCDataModel::$redisClient = YCRedisClient::getRedisClient();
        }
        return RPCDataModel::$redisClient;
    }

    public static function getData($proto, $configArray = array()) {
        if (isset($configArray['cache']['key']) && !empty($configArray['cache']['key']) && isset($configArray['cache']['time'])) {
            //有缓存key
        	if( isset($_GET['debug']) && 'delcache' == $_GET['debug'] ){
        		//不去读取redis，自然会走接口，并将最新的数据缓存
        	}else{
        		$cacheResult = RPCDataModel::getRedisClient()->getInterfaceCache($configArray['cache']['key']);
        		if (!empty($cacheResult)) {
                	return $cacheResult;
            	}
        	}
        }
        $result = RPCDataModel::getDataFromUBRPC($proto, $configArray);
        if (!empty($result)) {
            if (isset($configArray['cache']['key']) && !empty($configArray['cache']['key']) && isset($configArray['cache']['time'])) {
                RPCDataModel::getRedisClient()->saveInterfaceCahce($configArray['cache']['key'], $result, $configArray['cache']['time']);
            }
        }
        return $result;
    }

    public static function getDataFromUBRPC($proto, $configArray = array()) {
        $result = array();
        //记录参数log
        if (is_array($configArray['param'])) {
            $paramStr = "(";
            foreach ($configArray["param"] as $key => $val) {
                $paramStr .= $key . ":" . $val . ",";
            }
            $paramStr .= ")";
        }
        if (isset($GLOBALS["RPC_Return"])) {
            $GLOBALS["RPC_Return"] .= "action:" . $configArray["method"] . $paramStr;
        } else {
            $GLOBALS["RPC_Return"] = "action:" . $configArray["method"] . $paramStr;
        }
        $proto->rpcCall('server', $configArray['method'], $configArray['param'], $result);
        //是否打印接口返回参数
        if (PRINT_RPC_RESULT) {
            $GLOBALS["RPC_Return"] .= " rpcReturn[" . json_encode($result) . "]";
        }
        //是否有错
        $ErrorCode = $proto->getLastError();
        if ($ErrorCode) {
            Bingo_Log::warning('RPC_Error:' . $configArray['method'] . $paramStr . "[" . $ErrorCode["message"] . "]", LOG_DAL);
        } else {
            
        }
        return $result;
    }

}

