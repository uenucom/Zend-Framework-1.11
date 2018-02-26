<?php

class AlogList{
    /**
     * 获取log列表
     */
    public static function getLogListByPage($currentPage, $numPerPage, $role_name) {
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        if (intval($numPerPage) < 1) {
            $numPerPage = 20;
        }
        $listresult = array();
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_role_log', new Zend_Db_Expr("SQL_CALC_FOUND_ROWS * "));
        if ($role_name !== '') {
            $select = $select->where('username like ?', "%{$role_name}%");
        }
        $select = $select->order('id DESC')
                ->limitPage($currentPage, $numPerPage);
        //echo $select->__toString();exit();
        $result = $DB->fetchAll($select->__toString());

        $numsql = 'SELECT FOUND_ROWS() as countnum';
        $num = $DB->fetchRow($numsql);
        $totalpagenum = ceil($num['countnum'] / $numPerPage);

        if ($currentPage > $totalpagenum) {
            $currentPage = $totalpagenum;
            $select = $DB->select()
                    ->from('softrpc_user_role_log', new Zend_Db_Expr("SQL_CALC_FOUND_ROWS * "));

            if ($role_name !== '') {
                $select = $select->where('username like ?', "%{$role_name}%");
            }
            $select = $select->order('id DESC')
                    ->limitPage($currentPage, $numPerPage);
            $result = $DB->fetchAll($select->__toString());
        }
        $listresult['totalCount'] = $num['countnum']; //numPerPage pageNumShown currentPage
        $listresult['numPerPage'] = $numPerPage;
        $listresult['currentPage'] = $currentPage;
        $listresult['list'] = $result;
        return $listresult;
    }
    
    //获取某一条log
    public static function getLogInfoById($logid) {
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                //->from('softrpc_user_info', '*')
                //->joinLeft('softrpc_admin_role', 'softrpc_user_info.user_role = softrpc_admin_role.role_id', 'softrpc_admin_role.*')
                ->from('softrpc_user_role_log', '*')
                ->where('id = ?', $logid);
        $result = $DB->fetchRow($select->__toString());
        $result['log'] = str_replace(array('开通权限：','禁用权限：','新增用户'), array('<font color=green><b>开通权限：</b></font>','<font color=red><b>禁用权限：</b></font>','<font color=red><b>新增用户</b></font>'), $result['log']);
        return $result;
    }
}
