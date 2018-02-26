<?php
/**
 * 用户接口RPC配置
 *
 * @author xiaobo <xiaobo@duoku.com>
 * @version 2013-03-25 17:28$
 *
 */
include_once "mcpackrpc/rpc.php";

class Custom_AccountFunction {
	private $proto;
	function __construct($RpcServerConf=array()){
		$this->proto = new McpackRpc($RpcServerConf);
	}

        //oauth baidu
        public  function user_oauth_login($user_type, $oauth_id, $name, $channel, $qudao, $from, $version, $imei, $extension){
                $configArray = array(
			'method' => 'user_oauth_login',
			'param' => array(
                            'user_type' => $user_type,
			    'oauth_id' => $oauth_id,
                            'name' => $name,
                            'channel' => $channel,
                            'qudao' => $qudao,
                            'from' => $from,
                            'version' => $version,
                            'imei' => $imei,
                            'extension' => $extension,
                          //  'session' => $session,
                          //  'cookie' => $cookie,
			)
		);
		return RPCDataModel::getData($this->proto, $configArray);
        }

        public function get_user_info_by_user_id($user_id){
            $configArray = array(
			'method' => 'get_user_info_by_user_id',
			'param' => array(
				'user_id' => $user_id,
			)
		);
		return RPCDataModel::getData($this->proto, $configArray);
        }
	/**
	 * 根据传入的用户信息及操作，返回登录或注册的信息。
	 * @param string $user_info   //用户信息
	 * @param string $password   //密码
	 * @param unit32 $opt           //1登录/2注册
	 * @param unit32 $info_type  //用户信息类型 1-用户名 2-百度PassPort
	 * @param string $rmb_pwd  //保留session 天数 默认为15分钟
	 * @param string $from         //用户来源
	 * @return multitype:
	 */
	public function userRegLogin($user_info, $password, $opt, $info_type, $rmb_pwd, $from){
		$configArray = array(
			'method' => 'user_reg_login',
			'param' => array(
				'user_info' => $user_info,
				'password' => $password,
				'opt' => (int)$opt,
				'info_type' => (int)$info_type,
				'rmb_pwd' => $rmb_pwd,
				'from' => (string)$from
			)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}

/**
 * 新注册登录接口
 * @param unknown $user_info
 * @param unknown $password
 * @param unknown $opt
 * @param unknown $info_type
 * @param unknown $channel
 * @param unknown $qudao
 * @param unknown $from
 * @param unknown $version
 * @param unknown $imei_id
 * @param unknown $extension
 * @return Ambigous <multitype:, unknown>
 */
	public function userRegLoginEvolve($user_info, $password, $opt, $info_type,$channel, $qudao, $from, $version, $imei_id, $extension){
		$configArray = array(
			'method' => 'user_reg_login_evolve',
			'param' => array(
				'user_info' => $user_info,
				'password' => $password,
				'opt' => (int)$opt,
				'info_type' => (int)$info_type,
				'channel' => (int)$channel,
				'qudao' => (int)$qudao,
				'from' => (string)$from,
				'version' => (string)$version,
				'imei_id' => (string)$imei_id,
				'extension' => (string)$extension
			)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}

	/**
	 * 根据传入用户id和密码，对用户的密码进行重置
	 * @param string $ss_id  //session_id
	 * @param string $old_passworld
	 * @param string $new_password
	 * @return multitype:
	 */
	public function modifyUserPassword($ss_id, $old_password, $new_password){
		$configArray = array(
				'method' => 'modify_user_password',
				'param' => array(
						'ss_id' => $ss_id,
						'old_password' => $old_password,
						'new_password' => $new_password,
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}

	/**
	 * 根据传入的用户ID及手机号进行手机号绑定或解绑处理
	 * @param string $ss_id  session_id
	 * @param string $phone
	 * @param int $opt  1:绑定 2:解绑
	 * @return multitype:
	 */
	public function bindUserPhone($ss_id, $phone, $opt){
		$configArray = array(
				'method' => 'bind_user_phone',
				'param' => array(
						'ss_id' => $ss_id,
						'phone' => $phone,
						'opt' => (int)$opt,
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}

	/**
	 * 发手机发短信
	 * @param string $phone
	 * @param string $content
	 * @return 	0:成功 1:失败 2:	超过发送限制",	//默认每天同一手机号10条
	 */
	public function sendSms($phone, $content){
		$configArray = array(
				'method' => 'send_sms',
				'param' => array(
						'phone' => $phone,
						'content' => $content,
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}

	/**
	 * 后台上行短信监控
	 * @return multitype:
	 */
	public function  getSmsMonitor(){
		$configArray = array(
				'method' => 'get_sms_monitor',
				'param' => array()
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
	/**
	 * 获得用户绑定手机号
	 * @param string $ss_id
	 * @return multitype:
	 */
	public function getBindPhone($ss_id){
		$configArray = array(
				'method' => 'get_bind_phone',
				'param' => array(
						'ss_id' => $ss_id
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
	/**
	 * 用户退出
	 * @param string $ss_id
	 * @return 0:成功 -1:失败:
	 */
	public function userExit ($ss_id){
		$configArray = array(
				'method' => 'user_exit',
				'param' => array(
						'ss_id' => $ss_id
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
	/**
	 * 根据手机号获取用户名
	 * @param string $phone
	 * @return user_name:多酷用户名; third_name:第三方用户名
	 */
	public function checkBindPhone ($phone){
		$configArray = array(
				'method' => 'check_bind_phone',
				'param' => array(
						'phone' => $phone
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
	/**
	 *根据传入的的用户反馈信息记入数据库
	 * @param string $ss_id
	 * @param string $type_id
	 * @param string $feedback
	 * @param string $phone_num
	 * @return 0:-成功; -1:-失败:
	 */
	public function setUserFeedback ($ss_id, $type_id, $feedback, $phone_num){
		$configArray = array(
				'method' => 'set_user_feedback',
				'param' => array(
						'ss_id' => $ss_id,
						'type_id' => $type_id,
						'feedback' => $feedback,
						'phone_num' => $phone_num,
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
	/**
	 * 用户session刷新
	 * @param string $cookie
	 * @return 刷新用户session返回:
	 */
	public function refSsidBycookie ($cookie){
		$configArray = array(
				'method' => 'ref_ssid_bycookie',
				'param' => array(
						'cookie' => $cookie
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}

	/**
	 * 根据第三方信息，分配用户短信上行注册的随机码
	 * @param string $client_key
	 * @param string $game_id
	 * @param int $aid
	 * @param string $tmp_uid
	 * @return code:用户短信上行代码
	 */
	public function getRegisterCode($client_key, $game_id, $aid, $tmp_uid){
		$configArray = array(
				'method' => 'get_register_code',
				'param' => array(
						'client_key' => $client_key,
						'game_id' => $game_id,
						'aid' => (int)$aid,
						'tmp_uid' => $tmp_uid,
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}

	/**
	 * 根据多酷用户ID， 获取用户的临时uid。
	 * @param string $user_id
	 * @return tmp_uid:用户临时uid;
	 */
	public function getTmpUidByUser_id($user_id){
		$configArray = array(
				'method' => 'get_tmp_uid_by_user_id',
				'param' => array(
						'user_id' => $user_id
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
	/**
	 * 根据号段返回省份、城市及运营商
	 * @param string $phone_num
	 * @return numseg:手机号前7位（不使用可忽略）;result:处理结果;type:手机号类型;oid:省份ID;provice:对应的咱们的省份ID（mcp_province）;city:城市;code:城市代码;enabled:是否有效（目前都为有效状态）
	 */
	public function getPhoneAreaByPhoneNum($phone_num){
		$configArray = array(
				'method' => 'get_phone_area_by_phone_num',
				'param' => array(
						'phone_num' => $phone_num
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}

	/**
	 * 直接修改绑定手机号
	 * @param string $ss_id
	 * @param string $phone_num
	 * @return numseg:手机号前7位（不使用可忽略）;result:处理结果;type:手机号类型;oid:省份ID;provice:对应的咱们的省份ID（mcp_province）;city:城市;code:城市代码";enabled:是否有效（目前都为有效状态）
	 */
	public function changeUserPhoneNum($ss_id, $phone_num){
		$configArray = array(
				'method' => 'change_user_phone_num',
				'param' => array(
						'ss_id' => $ss_id,
						'phone_num' => $phone_num
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
	/**
	 * 根据用户名或手机号获取用户信息
	 * @param string $account session_id
 	 * @return multitype id name phone:
	 */
	public function getUserInfo($account){
		$configArray = array(
				'method' => 'get_user_info',
				'param' => array(
						'account' => $account
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
	/**
	 * 手机号一键登录注册
	 * @param unknown $phone
	 * @param unknown $from
	 * @return multitype result用户ID；session；cookie //结果 （ >0:用户ID， <0:表示登录注册失败,） 登录成功后返回用户ID
	 */
	public function userLoginByPhone($phone, $from){
		$configArray = array(
				'method' => 'user_login_by_phone',
				'param' => array(
						'phone' => $phone,
						'from' => $from
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
	/**
	 * 根据手机号和密码注册
	 * @param unknown $phone
	 * @param unknown $password
	 * @param unknown $from
	 * @return multitype: 结果 （ >0:用户ID， <0:表示登录注册失败,） 登录成功后返回用户ID
	 */
	public function userRegisterByPhone($phone, $password, $from){
		$configArray = array(
				'method' => 'user_register_by_phone',
				'param' => array(
						'phone' => $phone,
						'password' => $password,
						'from' => $from
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}

	/**
	 * 核对SESSION
	 * @param unknown $ssid
	 * @return multitype 失败-7:
	 */
	public function checkSession($ssid){
		$configArray = array(
				'method' => 'check_session',
				'param' => array(
						'ss_id' => $ssid
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
	/**
	 * 修改密码
	 * @param unknown $ssid
	 * @param unknown $pwd
	 * @return Ambigous <multitype:, unknown>
	 */
	public function changeUserPassword($ssid, $pwd){
		$configArray = array(
				'method' => 'change_user_password',
				'param' => array(
						'ss_id' => $ssid,
						'password' => $pwd
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}

	/**
	 * 获取用户信息
	 * @param unknown $ssid
	 * @return Ambigous <multitype:, unknown>
	 */
	public function getUserInfoBySs_id($ssid){
		$configArray = array(
				'method' => 'get_user_info_by_ss_id',
				'param' => array(
						'ss_id' => $ssid
				)
		);
		return RPCDataModel::getData($this->proto, $configArray);
	}
}