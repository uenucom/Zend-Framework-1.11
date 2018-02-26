<?php

class UserSession {

    /**
     * 获取角色列表
     */
    public static function getRoleListByPage($currentPage, $numPerPage, $user_name) {
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        if (intval($numPerPage) < 1) {
            $numPerPage = 20;
        }
        $listresult = array();
        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info_session', new Zend_Db_Expr("SQL_CALC_FOUND_ROWS * "));
        $select = $select->where('uid > ?', 0);
        if ($user_name !== '') {
            $select = $select->where('user_name like ?', "%{$user_name}%");
        }
        $select = $select->order('session_id DESC')
                ->limitPage($currentPage, $numPerPage);
        $result = $DB->fetchAll($select->__toString());

        $numsql = 'SELECT FOUND_ROWS() as countnum';
        $num = $DB->fetchRow($numsql);
        $totalpagenum = ceil($num['countnum'] / $numPerPage);

        if ($currentPage > $totalpagenum) {
            $currentPage = $totalpagenum;
            $select = $DB->select()
                    ->from('softrpc_user_info_session', new Zend_Db_Expr("SQL_CALC_FOUND_ROWS * "));
            $select = $select->where('uid > ?', 0);
            if ($user_name !== '') {
                $select = $select->where('user_name like ?', "%{$user_name}%");
            }
            $select = $select->order('session_id DESC')
                    ->limitPage($currentPage, $numPerPage);
            $result = $DB->fetchAll($select->__toString());
        }
        $listresult['totalCount'] = $num['countnum']; //numPerPage pageNumShown currentPage
        $listresult['numPerPage'] = $numPerPage;
        $listresult['currentPage'] = $currentPage;
        $listresult['list'] = $result;
        return $listresult;
    }

    /**
     * 根据$session_id删除
     * @param int $session_id
     */
    public static function del($session_id) {
        $DB = Zend_Registry::get('db');
        $where = $DB->quoteInto('session_id = ?', $session_id);
        $DB->delete('softrpc_user_info_session', $where);
    }

}
