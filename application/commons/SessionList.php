<?php

class SessionList {

    /**
     * 获取角色
     * @param type $session_id
     * @return array
     */
    public static function getInfoList($DB, $session_id) {
        $select = $DB->select()
                ->from('softrpc_user_info_session', array('uid', 'user_name', 'user_realname', 'ip', 'ua', 'encryption'))
                ->where('session_id = ?', $session_id)
                ->where('uid > ?', 0);
        $result = $DB->fetchRow($select->__toString());
        return $result;
    }

    /**
     * 更新会话信息
     * @param array $set
     * @param int $role_id
     */
    public static function updateInfoBySId($DB, $session_id, $data) {
        $where = $DB->quoteInto('session_id = ?', $session_id);
        $DB->update('softrpc_user_info_session', $data, $where);
    }

    /**
     * 删除旧有会话信息
     * @param int $user_id
     */
    public static function del($DB) {
        $where = $DB->quoteInto('modified+lifetime < ?', time());
        $DB->delete('softrpc_user_info_session', $where);
    }

    /**
     * 踢掉已登录用户
     * @param type $uid
     */
    public static function delSession($uid) {
        $DB = Zend_Registry::get('db');
        $where = $DB->quoteInto('uid = ?', $uid);
        $DB->delete('softrpc_user_info_session', $where);
    }

}
