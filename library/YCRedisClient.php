<?php

/**
 * Created by lihongming.
 * Date: 2013-3-28
 * Time: 下午 4:39:08
 */
class YCRedisClient {

    //单态实例
    static $obj = null;
    //是否为第一次连接,如果第一次连接失败，以后均不重复连接，以免过多连接导致页面加载时间过长
    static $firstTime = true;

    /**
     * 各个服务默认用的DB编号
     */
    //临时数据

    const TMP_BOOK_DB = 1;
    //支付相关订单相关信息
    const PAY_INFO_DB = 2;
    //接口数据
    const INTERFACE_DB = 3;

    //缓存Key前缀
    const USER_VOTE_BOOK_KEY = "USER_VOTE_BOOK_%s";
    const USER_UNPAY_KEY = "USER_UNPAY_KEY_%s";
    const USER_AUTOPAY_KEY = "USER_60M_AUTOPAY_KEY_%s";
    const USER_BOOK_PAYOK_BACK_URL = "USER_BOOK_PAYOK_BACK_URL_%s";

    function __construct($redisConfig = array()) {
        //echo "__construct";
        try {
            if (isset($redisConfig['host']) && isset($redisConfig['port'])) {
                $this->redis = new Redis();
                $this->redis->connect($redisConfig['host'], $redisConfig['port']);
            }
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
    }

    function __destruct() {
        //echo "__destruct";
        $this->redis->close();
    }

    /**
     * 取得实例
     * @return YCRedisClient
     */
    public static function getRedisClient() {
        if (empty(YCRedisClient::$obj) && YCRedisClient::$firstTime) {
            YCRedisClient::$obj = new YCRedisClient($GLOBALS['redisInterfaceCache']);
            YCRedisClient::$firstTime = false;
        }
        return YCRedisClient::$obj;
    }

    /**
     * 缓存接口数据
     * @param string $key
     * @param string $val
     * @param string $timeout
     */
    public function saveInterfaceCahce($key, $val, $timeout = 900) {
        try {
            $this->redis->select(YCRedisClient::INTERFACE_DB);
            $val = json_encode($val);
            $this->redis->setex($key, $timeout, $val);
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
    }

    /**
     * 提取缓存的数据
     * @param string $key
     * @return mixed
     */
    public function getInterfaceCache($key) {
        try {
            $this->redis->select(YCRedisClient::INTERFACE_DB);
            $val = $this->redis->get($key);
            $val = json_decode($val, true);
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
        return $val;
    }

    /**
     * 设置用户投票缓存,返回用户是否可以投票
     * @param string $userid
     * @return boolean
     */
    public function jdugeUserCanVoteBook($userid) {
        $key = sprintf(YCRedisClient::USER_VOTE_BOOK_KEY, $userid);
        $val = $this->getUserVoteBookCache($userid);
        try {
            $this->redis->select(YCRedisClient::TMP_BOOK_DB);
            if (!$val) {
                //不存在key
                //$this->redis->setex($key,  24*60*60,0);
                $now = 0;
                $this->redis->setex($key, strtotime(date('Y-m-d')) + 3600 * 24 - time(), $now);
            } else {
                //存在key，对该值进行+1
                $now = $val;
            }
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
        if ($now >= 3) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 获取用户投票缓存
     * @param string $userid
     */
    public function getUserVoteBookCache($userid) {
        try {
            $key = sprintf(YCRedisClient::USER_VOTE_BOOK_KEY, $userid);
            $this->redis->select(YCRedisClient::TMP_BOOK_DB);
            $val = $this->redis->get($key);
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
        if (empty($val)) {
            return false;
        } else {
            return $val;
        }
    }

    /**
     * 增加用户投票计数
     * @param string $userid
     */
    public function increaseUserVoteBookCntCache($userid) {
        $key = sprintf(YCRedisClient::USER_VOTE_BOOK_KEY, $userid);
        try {
            $this->redis->select(YCRedisClient::TMP_BOOK_DB);
            $this->redis->incr($key);
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
    }

    /**
     * 存储因支付密码、余额不足等引起的未完成订单
     * @param string $userid
     * @param string $orderid
     */
    public function saveUnpayOrderID($userid, $orderid) {
        $key = sprintf(YCRedisClient::USER_UNPAY_KEY, $userid);
        try {
            $this->redis->select(YCRedisClient::PAY_INFO_DB);
            $this->redis->setex($key, 24 * 60 * 60, $orderid);
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
    }

    /**
     * 获得因支付密码、余额不足等引起的未完成订单
     * @param string $userid
     * @return string $orderid
     */
    public function getUnpayOrderID($userid) {
        $key = sprintf(YCRedisClient::USER_UNPAY_KEY, $userid);
        $val = "";
        try {
            $this->redis->select(YCRedisClient::PAY_INFO_DB);
            $val = $this->redis->get($key);
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
        return $val;
    }

    /**
     * 设置用户是为自动支付
     * @param string $userid
     */
    public function saveUserAutoPay($userid) {
        $key = sprintf(YCRedisClient::USER_AUTOPAY_KEY, $userid);
        try {
            $this->redis->select(YCRedisClient::TMP_BOOK_DB);
            $this->redis->setex($key, 60 * 60, 1);
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
    }

    /**
     * 判断用户是否设置了自动付费，并且自动付费在有效期
     * @param string $userid
     * @return unknown
     */
    public function ifUserAutoPay($userid) {
        $key = sprintf(YCRedisClient::USER_AUTOPAY_KEY, $userid);
        $val = "";
        try {
            $this->redis->select(YCRedisClient::TMP_BOOK_DB);
            $val = $this->redis->get($key);
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
        if (empty($val)) {
            return false;
        }
        return true;
    }

    /**
     * 存储因支付密码、余额不足等引起的未完成订单
     * @param string $userid
     * @param string $orderid
     */
    public function savePayOkBackUrl($userid, $url) {
        $key = sprintf(YCRedisClient::USER_BOOK_PAYOK_BACK_URL, $userid);
        try {
            $this->redis->select(YCRedisClient::TMP_BOOK_DB);
            $this->redis->setex($key, 15 * 60, $url);
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
    }

    /**
     * 获得因支付密码、余额不足等引起的未完成订单
     * @param string $userid
     * @return string $orderid
     */
    public function getPayOkBackUrl($userid) {
        $key = sprintf(YCRedisClient::USER_BOOK_PAYOK_BACK_URL, $userid);
        $val = "";
        try {
            $this->redis->select(YCRedisClient::TMP_BOOK_DB);
            $val = $this->redis->get($key);
        } catch (Exception $e) {
            Bingo_Log::warning('Redis_Error:' . $e->getMessage(), LOG_DAL);
        }
        return $val;
    }

    /**
     * 判断redis服务是否可用
     */
    public function testRedis() {
        $key = 'test_redis';
        $val = 1;
        $message = '';
        try {
            $this->redis->select(YCRedisClient::TMP_BOOK_DB);
            $this->redis->setex($key, 30, $val);
            $v = $this->redis->get($key);
            if ($v == $val) {
                $message = 'ok';
            } else {
                $message = 'set get not equal';
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $message;
    }

}
