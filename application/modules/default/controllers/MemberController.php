<?php

/**
 * 登录操作
 */
class MemberController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        /* Initialize action controller here */
        $this->auth = Zend_Auth::getInstance()->getIdentity();
        if (isset($this->auth) && isset($this->auth->user_id) && isset($this->auth->user_name) && $this->auth->user_id > 0 && $this->auth->user_name != "") {
            $this->view->layout_user_name = $this->auth->user_realname;
            $this->view->layout_user_id = $this->auth->user_id;
            $this->view->menulist = MenuInit::doAct(MenuInit::getList(), MenuInit::unserialize_act($this->auth->role_menu));
        } else {
            //$this ->render('/member');
            //$this->_forward('index', "member", "default", array($params = null));
        }
        DB::conn('db');
    }

    /** 登录页面 */
    public function indexAction() {
        
    }

    /**
     * 登录操作
     */
    public function loginAction() {
        header("Content-type:text/html;charset=utf-8");
        $this->_helper->viewRenderer->setNoRender(true);
        $message = "";
        if ($this->_request->isPost()) {
            $filter = new Zend_Filter_StripTags();
            $username = $filter->filter($this->_request->getPost('username'));
            $password = $filter->filter($this->_request->getPost('password'));
            $code = $filter->filter($this->_request->getPost('code'));
            $admincpcode = new Zend_Session_Namespace('Verification_Code');
            if ($admincpcode->imagecode == $code && $username != '' && $password != '') {
                //域用户登录
                /**
				$ldap_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/ldap.ini', null, true);
                $ldap_conn = ldap_connect($ldap_config->config->hostname, $ldap_config->config->port);
                ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
                $ldapUserName = $username . $ldap_config->config->dn;
                $login_result = @ldap_bind($ldap_conn, $ldapUserName, $password);
                ldap_close($ldap_conn);
                $inforesult = array();
                if ($login_result) {
                    $info = UserList::getUserInfo($username);
                    if (empty($info)) {
                        $inforesult['code'] = 0;
                        $inforesult['message'] = "登录失败";
                        echo json_encode($inforesult);
                        exit();
                    } else {
                        $info['user_id'];
                        SessionList::delSession($info['user_id']);
                        $info = (object) $info;
                        $auth = Zend_Auth::getInstance();
                        $session = new Zend_Session_Namespace($auth->getStorage()->getNamespace());
                        $session->setExpirationSeconds(strtotime('1 day', 0));
                        $auth->getStorage()->write($info);
                        $inforesult['code'] = 1;
                        $inforesult['message'] = "登录成功";
                        $inforesult['url'] = "/";
                        echo json_encode($inforesult);
                        exit();
                    }
                } else {
					*/
                    $dbAdapter = Zend_Registry::get('db');
                    $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
                    $authAdapter->setTableName('softrpc_user_info')
                            ->setIdentityColumn('user_name')
                            ->setCredentialColumn('user_password')
                            ->setIdentity($username)
                            ->setCredential($password)
                            ->setCredential(hash('MD5', $password));
                    $auth = Zend_Auth::getInstance();
                    $result = $auth->authenticate($authAdapter);
                    if ($result->isValid()) {
                        $session = new Zend_Session_Namespace($auth->getStorage()->getNamespace());
                        $session->setExpirationSeconds(strtotime('1 day', 0));
                        $info = UserList::getUserInfo($username);
                        if (empty($info)) {
                            $inforesult['code'] = 0;
                            $inforesult['message'] = "登录失败";
                            echo json_encode($inforesult);
                            exit();
                        } else {
                            SessionList::delSession($info['user_id']);
                            $info = (object) $info;
                            $auth->getStorage()->write($info);
                            $inforesult['code'] = 1;
                            $inforesult['message'] = "登录成功";
                            $inforesult['url'] = "/";
                            echo json_encode($inforesult);
                            exit();
                        }
                    } else {
                        $inforesult['code'] = 0;
                        $inforesult['message'] = "用户名或密码错误";
                        echo json_encode($inforesult);
                        exit();
                    }
                //}
            } else {
                $inforesult['code'] = -1;
                $inforesult['message'] = "验证码错误";
                echo json_encode($inforesult);
                exit();
            }
        } else {
            echo "deny";
        }
    }

    /**
     * 验证码
     */
    public function verifycodeAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $admincode = new Custom_Controller_Plugin_ImgCode();
        $admincode->image2();
    }

    /**
     * 用户退出
     */
    public function logoutAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            Zend_Auth::getInstance()->clearIdentity();
            Zend_Session::destroy();
            $inforesult = array();
            $inforesult['statusCode'] = '301';
            $inforesult['message'] = '用户已退出';
            $inforesult['navTabId'] = '';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = '';
            $inforesult['forwardUrl'] = '/member';
            echo json_encode($inforesult);
            exit();
        } else {
            Zend_Auth::getInstance()->clearIdentity();
            Zend_Session::destroy();
            $this->_redirect('/member');
            unset($_COOKIE);
        }
    }

    /**
     * 系统配置
     */
    public function loadxmlAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        header("Content-type:text/xml;charset=utf-8");
        $xmlinfo = file_get_contents(APPLICATION_PATH . '/configs/frag_config.xml');
        echo $xmlinfo;
    }

}
