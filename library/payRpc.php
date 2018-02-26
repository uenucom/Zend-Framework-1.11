<?php
/**
 *  return a singleton instance of PayRpc
 *
 * @author zhangjian07
 */
include_once "mcpackrpc/rpc.php";

class PayRpc {
    
    public static function getMcpackRpc(){
          include_once dirname(__FILE__).'/rpcConfig.php';
          $proto = new McpackRpc($rpcArrConf);
          return $proto;
    }

}

?>
