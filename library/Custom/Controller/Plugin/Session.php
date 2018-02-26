
<?php

/**

 * SessionMysql 数据库存储类

 */
//defined('IN_QIAN') or exit('Access Denied');

class Custom_Controller_Plugin_Session  implements Zend_Session_SaveHandler_Interface{

    public $lifetime = 1800; // 有效期，单位：秒（s），默认30分钟
    public $db;
    public $table;

    /**

     * 构造函数

     */
    public function __construct() {

        $this->db = Zend_Registry::get('dbAdapter');
        $this->lifetime = 1800;
        session_set_save_handler(
                array(&$this, 'open'), // 在运行session_start()时执行
                array(&$this, 'close'), // 在脚本执行完成 或 调用session_write_close() 或 session_destroy()时被执行，即在所有session操作完后被执行
                array(&$this, 'read'), // 在运行session_start()时执行，因为在session_start时，会去read当前session数据
                array(&$this, 'write'), // 此方法在脚本结束和使用session_write_close()强制提交SESSION数据时执行
                array(&$this, 'destroy'), // 在运行session_destroy()时执行
                array(&$this, 'gc') // 执行概率由session.gc_probability 和 session.gc_divisor的值决定，时机是在open，read之后，session_start会相继执行open，read和gc
        );
        //session_start(); // 这也是必须的，打开session，必须在session_set_save_handler后面执行
        Zend_Session::start();
    }

    /**
     * session_set_save_handler open方法
     *
     * @param $savePath
     * @param $sessionName
     * @return true
     */
    public function open($savePath, $sessionName) {
        return true;
    }

    /**
     * session_set_save_handler close方法
     *
     * @return bool
     */
    public function close() {
        return $this->gc($this->lifetime);
    }

    /**
     * 读取session_id
     *
     * session_set_save_handler read方法
     * @return string 读取session_id
     */
    public function read($sessionId) {
//        $condition = array(
//            'where' => array(
//                'session_id' => $sessionId
//            ),
//            'fields' => 'session_data'
//        );
//        $DB = Zend_Registry::get('db');
        $select = $DB->select()
                ->from('softrpc_user_info_session', array('session_data'))
                ->where('session_id = ?', $sessionId);
        $result = $this->db->fetchRow($select->__toString());
        return $row ? $row['session_data'] : '';
    }

    /**
     * 写入session_id 的值
     *
     * @param $sessionId 会话ID
     * @param $data 值
     * @return mixed query 执行结果
     */
    public function write($sessionId, $data) {
        $uid = isset($_SESSION['uid']) ? $_SESSION['uid'] : 0;
//        $roleId = isset($_SESSION['roleId']) ? $_SESSION['roleId'] : 0;
//        $grouId = isset($_SESSION['grouId']) ? $_SESSION['grouId'] : 0;
//        $m = defined('ROUTE_M') ? ROUTE_M : '';
//        $c = defined('ROUTE_C') ? ROUTE_C : '';
//        $a = defined('ROUTE_A') ? ROUTE_A : '';
        if (strlen($session_data) > 255) {
            $session_data = '';
        }
//        $ip = get_ip();
        $sessionData = array(
            'session_id' => $sessionId,
            'name' => $name,
            'modified' => $sessionId,
            'lifetime' => $lifetime,
            'uid' => $userId,
            'ip' => '127.0.0.1',
//            'last_visit' => SYS_TIME,
//            'role_id' => $roleId,
//            'group_id' => $grouId,
//            'm' => $m,
//            'c' => $c,
//            'a' => $a,
            'session_data' => $session_data,
        );
//        return $this->db->insert($sessionData, 1, 1);
        return $DB->insert('softrpc_user_info_session', $sessionData);
    }

    /**
     * 删除指定的session_id
     *
     * @param string $sessionId 会话ID
     * @return bool
     */
    public function destroy($sessionId) {
//        return $this->db->delete(array('session_id' => $sessionId));
//        return $this->db->delete(array('session_id' => $sessionId));

        $where = $this->db->quoteInto('session_id = ?', $sessionId);
        return $this->db->delete('softrpc_user_info_session', $where);
    }

    /**
     * 删除过期的 session
     *
     * @param $lifetime session有效期（单位：秒）
     * @return bool
     */
    public function gc($lifetime) {
//        $expireTime = SYS_TIME - $lifetime;
//        return $this->db->delete("`last_visit`<$expireTime");
        $where = $this->db->quoteInto('modified < ?', time()- $this->lifetime);
        return $this->db->delete('softrpc_user_info_session', $where);
    }

}
