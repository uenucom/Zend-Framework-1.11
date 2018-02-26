<?php

/**
 * 默认主页
 */
class Book_IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        $this->auth = Zend_Auth::getInstance()->getIdentity();
        if (isset($this->auth) && $this->auth->user_id > 0 && $this->auth->user_name != "") {
            $this->view->layout_user_name = $this->auth->user_realname;
            $this->view->layout_user_id = $this->auth->user_id;
            //RoleList::getAdmission($this->auth, $this->_request, $this->_helper); //权限控制
            $this->view->menulist = MenuInit::doAct(MenuInit::getList(), MenuInit::unserialize_act($this->auth->role_menu));
        } else {
            RoleList::doForward($this, $this->_request);
        }
    }

    /**
     * 我的主页
     */
    public function indexAction() {
        
    }

}
