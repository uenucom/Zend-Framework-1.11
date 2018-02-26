<?php
class Custom_Checkid {

	//平台id
	public static function checkAppid($plateid){
		DB::connSource('dbappid');
		$db = Zend_Registry::get('dbappid');
		$select = $db->select();
		$select->from('pt_game_basic_info',"*");
		$select->where('enable = ?', 1);
		$select->where('app_id = ?', $plateid);
		$sql = $select->__toString();
		$result = $db->fetchRow($sql);
		if($result){
			return  $result;
		} else {
			return  null;
		}

	}

	//资源id
	public static function checkSource($appid){

		DB::connPlateform('dbplateform');
		$db = Zend_Registry::get('dbplateform');
		$select = $db->select();
		$select->from('mcp_content',"*");
		$select->where('visible = ?', 1);
		$select->where('enable = ?', 1);
		$select->where('status = ?', 1);
		$select->where('id = ?', $appid);
		$sql = $select->__toString();
		$result = $db->fetchRow($sql);
		if($result){
			return  $result;
		} else {
			return  null;
		}
	}
}