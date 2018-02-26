<?php

/**
 * 角色管理
 */
class SessionController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->_helper->layout()->disableLayout();
        $this->auth = Zend_Auth::getInstance()->getIdentity();
        if (!is_null($this->auth) && $this->auth->user_id > 0 && $this->auth->user_name != "") {
            $this->view->layout_user_name = $this->auth->user_realname;
            $this->view->layout_user_id = $this->auth->user_id;
            RoleList::getAdmission($this->auth, $this->_request, $this->_helper); //权限控制器
            $this->view->menulist = MenuInit::doAct(MenuInit::getList(), MenuInit::unserialize_act($this->auth->role_menu));
        } else {
            RoleList::doForward($this, $this->_request);
        }
        DB::conn('db');
    }

    /**
     * 角色列表
     */
    public function indexAction() {
        $filter = new Zend_Filter_StripTags();
        if ($this->_request->isPost()) {
            $currentPage = trim($filter->filter($this->_request->getPost('pageNum', 1))); //getPost
            $numPerPage = trim($filter->filter($this->_request->getPost('numPerPage', 20)));
            $user_name = trim($filter->filter($this->_request->getPost('user_name')));
        } else {
            $currentPage = trim($filter->filter($this->_request->getParam('pageNum', 1))); //getPost
            $numPerPage = trim($filter->filter($this->_request->getParam('numPerPage', 20)));
            $user_name = trim($filter->filter($this->_request->getParam('user_name')));
        }
        $sessionlist = UserSession::getRoleListByPage($currentPage, $numPerPage, $user_name);
        $this->view->sessionlist = $sessionlist;
        $this->view->user_name = $user_name;
    }

    /**
     * 删除角色
     */
    public function delAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $filter = new Zend_Filter_StripTags();
        $session_id = trim($filter->filter($this->_request->getParam('session_id')));
        if ($session_id) {
            $num = UserSession::del($session_id);
            $current_session_id = Zend_Session::getId();
            if ($session_id == $current_session_id) {
                Zend_Auth::getInstance()->clearIdentity();
                Zend_Session::destroy();
            }
            $inforesult = array();
            $inforesult['statusCode'] = '200';
            $inforesult['message'] = '操作成功';
            $inforesult['navTabId'] = 'session_list';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = '';
            $inforesult['forwardUrl'] = '';
            $inforesult['confirmMsg'] = '';
            echo json_encode($inforesult);
        } else {
            $inforesult = array();
            $inforesult['statusCode'] = '300';
            $inforesult['message'] = '操作失败';
            $inforesult['navTabId'] = 'session_list';
            $inforesult['rel'] = '';
            $inforesult['callbackType'] = '';
            $inforesult['forwardUrl'] = '';
            $inforesult['confirmMsg'] = '';
            echo json_encode($inforesult);
        }
    }

}
