<?php

/**
 * 权限日志
 */
class AlogController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender();
        $this->auth = Zend_Auth::getInstance()->getIdentity();
        if (isset($this->auth) && $this->auth->user_id > 0 && $this->auth->user_name != "") {
            $this->view->layout_user_name = $this->auth->user_realname;
            $this->view->layout_user_id = $this->auth->user_id;
            RoleList::getAdmission($this->auth, $this->_request, $this->_helper);
            $this->view->menulist = MenuInit::doAct(MenuInit::getList(), MenuInit::unserialize_act($this->auth->role_menu));
        } else {
            RoleList::doForward($this, $this->_request);
        }
        DB::conn('db');
    }

    /**
     * 浏览权限
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
        $loglist = AlogList::getLogListByPage($currentPage, $numPerPage, $user_name);
        $this->view->user_name = $user_name;
        $this->view->loglist = $loglist;
    }

    public function showAction() {
        $filter = new Zend_Filter_StripTags();
        $logid = trim($filter->filter($this->_request->getParam('logid')));
        $loglinfo = AlogList::getLogInfoById($logid);
        $this->view->loglinfo = $loglinfo;
    }

}
