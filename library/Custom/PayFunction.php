<?php

include_once "mcpackrpc/rpc.php";

class Custom_PayFunction {

    private $proto;

    function __construct($RpcServerConf = array()) {
        $this->proto = new McpackRpc($RpcServerConf);
    }

    /**
     * 根据充值id获得充值类型名称
     * @param unknown_type $id
     * @return string
     */
    public function getChargeTypeNameById($id) {
        $map = array(
            '0' => '联动优势短信',
            '1' => '移动充值卡',
            '2' => '联通充值卡',
            '3' => '电信充值卡',
            '4' => '骏网一卡通',
            '5' => '盛大一卡通',
            '6' => '网易一卡通',
            '7' => '征途卡',
            '8' => '搜狐卡',
            '9' => '完美卡',
            '10' => 'Q币卡',
            '11' => '久游卡',
            '12' => '天下一卡通',
            '13' => '纵游一卡通',
            '14' => '天宏一卡通',
            '15' => '商联通卡',
            '16' => '奥斯卡',
            '17' => '亿卡通',
            '18' => '新浪短信',
            '19' => '湖南电信wap计费',
            '20' => '搜狐联通短信',
            '21' => '移动游戏基地wap充值',
            '22' => '联通华建短信',
            '23' => '远盛电信短信',
            '101' => '支付宝',
            '102' => '财付通',
            '103' => '易宝银行卡',
            '104' => '快钱银行卡',
            '105' => '3G泡泡wap充值',
            '200' => '助威奥运活动赠送',
            '201' => '嫦娥悬赏',
            '202' => '社区微小说活动',
            '203' => '赠送酷币',
            '204' => '首次充值返还',
            '205' => 'vip充值返还',
            '206' => '光棍活动',
            '207' => '书城光棍节活动',
            '208' => '社区圣诞活动',
            '209' => '岁末签到'
        );
        if (isset($map[$id])) {
            return $map[$id];
        } else {
            if (strlen("$id") > 9) {
                return "短信($id)";
            } else {
                return '其他';
            }
        }
    }

    /**
     * 酷点转酷币
     * @param string $kudian
     * @return number
     */
    public function kudianToKubi($kudian) {
        if ($kudian > 0 && is_numeric($kudian)) {
            return $kudian / 100;
        } else {
            return 0;
        }
    }

    /**
     * 根据充值状态码 获取充值状态名称
     * @param string $id
     * @return string
     */
    public function getChargeStatusNameById($id) {
        $map = array(
            '0' => '充值中',
            '1' => '成功',
            '2' => '失败'
        );
        return isset($map[$id]) ? $map[$id] : '未知';
    }

    /**
     * 查询支付纪录
     * @param type $ssid sessionid
     * @param type $payType 支付类型，1：充值记录，2：消费记录，3：短信购买
     * @param type $date 0：返回本周，年份-月份返回指定月份，-年-月查询某月之前所有
     * @param type $page_num 指定第几页
     * @param type $page_cnt 每页显示条数
     *
     * @return result: array : 充值查询：[0发生时间，1充值类型，2充值金额，3订单号,4充值状态]
     *           消费: [0发生时间，1购买商品名称，2消费酷币，3消费酷点]， -7：session不存在
     *           短信查询：[0发生时间， 1购买商品名称，2消费金额]
     *          record_cnt:记录总条数
     */
    public function getUserPayHistory($ssid, $pay_type, $date, $page, $pagesize) {
        $configArray = array(
            'method' => 'get_user_pay_history',
            "param" => array(
                'ss_id' => $ssid,
                'pay_type' => $pay_type,
                'date' => $date,
                'page_num' => $page,
                'page_cnt' => $pagesize,
            ),
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

    /**
     * 查询用户支付密码状态
     * @param type $ssid session id
     * @return 0:未设置，1:已设置未开启,2:已开启，-7：session不存在
     */
    public function userPayPwdStat($ssid) {
        $configArray = array(
            "method" => "user_paypwd_stat",
            "param" => array(
                'ss_id' => $ssid,
            ),
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

    /**
     * 重置支付密码
     * @param type $ssid sessionid
     * @param type $opt  支付操作，0：关闭支付密码，1：开启，2:重置
     * @param type $oldPayPwd 旧密码
     * @param type $newPayPwd 新密码
     * @return 0:操作成功，1：密码错误，2：操作失败，-7:session不存在
     */
    public function setPayPwd($ssid, $opt, $oldPayPwd = null, $newPayPwd = null) {

        $configArray = array(
            "method" => "set_paypwd",
            "param" => array(
                'ss_id' => $ssid,
                'old_paypwd' => $oldPayPwd,
                'new_paypwd' => $newPayPwd,
                'opt' => $opt,
            ),
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

    /**
     * 绑定手机号码，设置支付密码
     * @param type $ssid sessionid
     * @param type $passpwd 用户密码
     * @param type $phoneNum  手机号码
     * @return 0:成功，1：失败，-7：session不存在
     */
    public function bindPayPwdByPhone($ssid, $passpwd, $phoneNum) {

        $configArray = array(
            "method" => "bind_paypwd_by_phone",
            "param" => array(
                'ss_id' => $ssid,
                'passpwd' => $passpwd,
                'phone_num' => $phoneNum,
            ),
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

    /**
     *  查询用户帐户余额
     * @param type $ssid ssid
     * @return array(kudian, jifen), -7:session不存在
     */
    public function queryUserKubi($ssid) {
        $configArray = array(
            "method" => "query_user_kubi",
            "param" => array(
                'ss_id' => $ssid,
            ),
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

    /**
     * 短信充值新接口
     * @param string $ssid
     * @param string $phoneNum
     * @param int $payNum
     * @param string $channelId
     * @param int $payType
     * @param string $payOrder
     * @param int $bind
     * @return array
     */
    public function duokooSmsRechargeNew($ssid, $phoneNum, $payNum, $channelId, $payType, $payOrder, $bind) {
        $configArray = array(
            "method" => "duokoo_sms_recharge_new",
            "param" => array(
                'ss_id' => $ssid,
                'phone_num' => $phoneNum,
                'pay_num' => $payNum,
                'channel_id' => $channelId,
                'pay_type' => $payType,
                'ex_channel' => 0,
                'version' => '1.0.0.25',
                'imei' => '',
                'extension' => !empty($_GET['fr']) ? trim($_GET['fr']) : '',
                'pay_oder' => $payOrder,
                'bind_flag' => $bind
            )
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

    /**
     * 生成短信充值订单
     * @param String $ssid
     * @param String $phoneNum
     * @param int $payNum
     * @param String $channelId
     * @param int $payType
     * @param String $payOrder
     * @param int $bind
     * @return array
     */
    public function duokooSmsRecharge($ssid, $phoneNum, $payNum, $channelId, $payType, $payOrder, $bind) {
        $configArray = array(
            "method" => "duokoo_sms_recharge",
            "param" => array(
                'ss_id' => $ssid,
                'phone_num' => $phoneNum,
                'pay_num' => $payNum,
                'channel_id' => $channelId,
                'pay_type' => $payType,
                'pay_oder' => $payOrder,
                'bind_flag' => $bind
            )
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

    /**
     * 生成充值卡充值订单
     * @param int $userId
     * @param String $cardNum
     * @param String $cardPwd
     * @param int $cardType
     * @param int $amount
     * @param int $inChannel
     * @param int $exChannel
     * @param String $imei
     * @param String $version
     * @param String $extension
     * @param String $pay_order
     * @return array
     */
    public function cardRechargeByUserId($userId, $cardNum, $cardPwd, $cardType, $amount, $inChannel, $exChannel, $imei, $version, $extension, $pay_order) {
        $configArray = array(
            "method" => "card_recharge_by_user_id",
            "param" => array(
                'user_id' => $userId,
                'card_num' => $cardNum,
                'card_pwd' => $cardPwd,
                'card_type' => $cardType,
                'amount' => $amount,
                'in_channel' => $inChannel,
                'ex_channel' => $exChannel,
                'imei' => $imei,
                'version' => $version,
                'extension' => $extension,
                'pay_order' => $pay_order
            )
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

    /**
     * 查询订单状态
     * @param string $orderId
     * @return array
     */
    public function checkPayment($orderId) {
        $configArray = array(
            "method" => "check_payment",
            "param" => array(
                'order_id' => $orderId
            )
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

    /**
     * 生成银行卡充值订单
     * @param String $ssid
     * @param int $gatewayType
     * @param String $cardType
     * @param double $amount
     * @param int $inChannel
     * @param int $exChannel
     * @param String $version
     * @param String $imei
     * @param String $extension
     * @param String $payOrder
     * @return array
     */
    public function bankCardRecharge($ssid, $gatewayType, $cardType, $amount, $inChannel, $exChannel, $version, $imei, $extension, $payOrder) {
        $configArray = array(
            "method" => "bank_card_recharge",
            "param" => array(
                'ss_id' => $ssid,
                'gateway_type' => $gatewayType,
                'card_type' => $cardType,
                'amount' => $amount,
                'in_channel' => $inChannel,
                'ex_channel' => $exChannel,
                'version' => $version,
                'imei' => $imei,
                'extension' => $extension,
                'pay_order' => $payOrder
            )
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

    /**
     * 用密码支付
     * @param String $orderId
     * @param int $recPwd
     * @param String $payPassword
     * @return array
     */
    public function usePwdPay($orderId, $recPwd, $payPassword) {
        $configArray = array(
            "method" => "use_pwd_pay",
            "param" => array(
                'order_id' => $orderId,
                'rec_pwd' => $recPwd,
                'pay_password' => $payPassword
            )
        );
        return RPCDataModel::getData($this->proto, $configArray);
    }

}