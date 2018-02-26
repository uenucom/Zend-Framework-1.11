<?php

class Custom_Db_Profilerlog extends Zend_Db_Profiler {

    /**
     * Zend_Log instance
     * @var Zend_Log
     */
    protected $_log;

    /**
     * counter of the total elapsed time
     * @var double 
     */
    protected $_totalElapsedTime;

    public function __construct($enabled = false) {
        parent::__construct($enabled);
        $date = date("Y-m-d", time());
        $this->_log = Zend_Log::factory(array(
                    'timestampFormat' => 'Y-m-d H:i:s',
                    array(
                        'writerName' => 'Stream',
                        'writerParams' => array(
                            'stream' => APPLICATION_PATH . "/../../logs/managedb-{$date}.log",
                        ),
                        'formatterName' => 'Simple',
                        'formatterParams' => array(
                            'format' => '%timestamp% %message% [Request_Time:%info%s]' . "\r\n",
                        ),
                        'filterName' => 'Priority',
                        'filterParams' => array(
                            'priority' => Zend_Log::WARN,
                        ),
                    )
        ));
    }

    /**
     * Intercept the query end and log the profiling data.
     *
     * @param  integer $queryId
     * @throws Zend_Db_Profiler_Exception
     * @return void
     */
    public function queryEnd($queryId) {
        $state = parent::queryEnd($queryId);

        if (!$this->getEnabled() || $state == self::IGNORED) {
            return;
        }

        $this->setFilterQueryType(Zend_Db_Profiler::SELECT | Zend_Db_Profiler::INSERT | Zend_Db_Profiler::UPDATE | Zend_Db_Profiler::TRANSACTION | Zend_Db_Profiler::UPDATE | Zend_Db_Profiler::QUERY | Zend_Db_Profiler::CONNECT);
        // get profile of the current query
        $profile = $this->getQueryProfile($queryId);
        // update totalElapsedTime counter
        $this->_totalElapsedTime += $profile->getElapsedSecs();
        // create the message to be logged
        $sql = $profile->getQuery();
        $sqlparams = $profile->getQueryParams();
        $usedTime = number_format($profile->getElapsedSecs(), 6, '.', '');
        $auth = Zend_Auth::getInstance()->getIdentity();
        if (isset($auth) && isset($auth->user_id)&& isset($auth->user_name) ){
            if (count($sqlparams)) {
                $do_sql = '';
                $arr = explode('?', $sql);
                foreach ($arr as $num => $var) {
                    $nums = $num + 1;
                    if ($nums <= count($sqlparams)) {
                        $do_sql .= $var . "'{$sqlparams[$nums]}'";
                    } else {
                        $do_sql .= $var;
                    }
                }
                $message = "[GUID:".GUID_CODE."] [UID:{$auth->user_id}] [UserName:{$auth->user_realname}($auth->user_name)] [IP: " . Custom_Controller_Plugin_Ipaddress::getIP() . "] [SQL:{$do_sql}]";
            } else {
                $message = "[GUID:".GUID_CODE."] [UID:{$auth->user_id}] [UserName:{$auth->user_realname}($auth->user_name)] [IP: " . Custom_Controller_Plugin_Ipaddress::getIP() . "] [SQL: " . $sql . "]";
            }
        } else {
            $message = "[GUID:".GUID_CODE."] [UserName:guest] [IP: " . Custom_Controller_Plugin_Ipaddress::getIP() . "] [SQL: " . $sql . "]";
        }

        $this->_log->log($message, 2, $usedTime);
    }

}
