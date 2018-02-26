<?php

class Custom_Controller_Plugin_Onlypager implements Zend_Paginator_Adapter_Interface {

    public function __construct($dataresult, $totalnum, $offset, $itemCountPerPage) {
        $this->result = $dataresult;
        $this->count = $totalnum;
    }

    public function getItems($offset, $itemCountPerPage) {
        return $this->result;
    }

    //获取所有记录行数
    public function count() {
        return $this->count;
    }

}
