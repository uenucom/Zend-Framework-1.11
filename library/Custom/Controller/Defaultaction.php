<?php

/**
 * Custom
 *
 * LICENSE:
 *
 * @category   Custom
 * @package    Custom
 * @subpackage Controller
 * @copyright  Copyright (c)
 * @license
 * @version
 */

/**
 * Custom super action controller.
 *
 * @category   Custom
 * @package    Custom
 * @subpackage Controller
 * @author
 * @copyright  Copyright (c)
 * @license
 */
class Custom_Controller_Defaultaction extends Zend_Controller_Action {
	public function init(){
		/*
		 * 商城
		 */
		$thriftconfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/thrift.ini', null, true);
		if (APPLICATION_ENV === 'development') {
			Zend_Registry::set('thriftconfig', $thriftconfig->offline);
			$configthrift = Zend_Registry::get('thriftconfig');
		} elseif(APPLICATION_ENV === 'developmentqa') {
			Zend_Registry::set('thriftconfig', $thriftconfig->offlineqa);
			$configthrift = Zend_Registry::get('thriftconfig');
		} else {
			Zend_Registry::set('thriftconfig', $thriftconfig->online);
			$configthrift = Zend_Registry::get('thriftconfig');
		}

		$MallObj  = new Custom_ThriftMallRPC(array('Maxnum'=>$configthrift->mall->Maxnum,'ip'=>$configthrift->mall->ip,'port'=>$configthrift->mall->port,'timeout'=>$configthrift->mall->timeout));

		Zend_Registry::set('MallObj', $MallObj);
		
		$UserObj  = new Custom_ThriftUserRPC(array('Maxnum'=>$configthrift->user->Maxnum,'ip'=>$configthrift->user->ip,'port'=>$configthrift->user->port,'timeout'=>$configthrift->user->timeout));
		Zend_Registry::set('UserObj', $UserObj);
		
		$PackageObj = new Custom_ThriftPackageRPC(array('Maxnum'=>$configthrift->pack->Maxnum,'ip'=>$configthrift->pack->ip,'port'=>$configthrift->pack->port,'timeout'=>$configthrift->pack->timeout));
		Zend_Registry::set('PackageObj', $PackageObj);

		$LotteryObj  = new Custom_ThriftLotteryRPC(array('Maxnum'=>$configthrift->lottery->Maxnum,'ip'=>$configthrift->lottery->ip,'port'=>$configthrift->lottery->port,'timeout'=>$configthrift->lottery->timeout));
		Zend_Registry::set('LotteryObj', $LotteryObj);

		$StragegyObj = new Custom_ThriftStrategyRPC(array('Maxnum'=>$configthrift->stragegy->Maxnum,'ip'=>$configthrift->stragegy->ip,'port'=>$configthrift->stragegy->port,'timeout'=>$configthrift->stragegy->timeout));
		Zend_Registry::set('StragegyObj', $StragegyObj);
	}
}
