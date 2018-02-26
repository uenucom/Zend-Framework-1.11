<?php

class Custom_Controller_Plugin_Mypager implements Zend_Paginator_Adapter_Interface {

    public function __construct($sql, $offset, $itemCountPerPage) {
        $DB = Zend_Registry::get('dbAdapter');
        //$newsql = $sql.' limit '.$offset .','.$itemCountPerPage;
        $numsql = 'SELECT FOUND_ROWS() as countnum';
        //启用缓存机制
        //$Tools       = new Tools();
        //$result = $Tools -> setZendCache("default", $select ->__toString(), "fetchAll", "");
        $result = $DB->fetchAll($sql);
        //$num = $Tools -> setZendCache("default", $numsql, "fetchAll", "");
        $num = $DB->fetchRow($numsql);
        $this->result = $result;
        $this->count = $num['countnum'];
    }

//getItems
//	public function getItems($offset, $itemCountPerPage)
//	{
//		$select = $DB->select()
//		->from('video_lists' , '*')
//		->where('list_type = ?', $module)
//		->order('list_createtime DESC')
//		->limit($offset, $itemCountPerPage);
//		return $result;
//
//		//return $this->table->find('all', array('limit' => $itemCountPerPage, 'offset' => $offset, 'conditions' => $this->conditions));
//	}
//

    public function getItems($offset, $itemCountPerPage) {
        return $this->result;
    }

    //获取所有记录行数
    public function count() {
        return $this->count;
    }

}
