<?php

class UserexcelController extends Zend_Controller_Action {

    public function init() {
        $this->_helper->layout()->disableLayout(); //enableLayout
        $this->auth = Zend_Auth::getInstance()->getIdentity();
        if (isset($this->auth) && $this->auth->user_id > 0 && $this->auth->user_name != "") {
//            $this->view->layout_user_name = $this->auth->user_realname;
//            $this->view->layout_user_id = $this->auth->user_id;
            RoleList::getAdmission($this->auth, $this->_request, $this->_helper);
//            $this->view->menulist = MenuInit::doAct(MenuInit::getList(), MenuInit::unserialize_act($this->auth->role_menu));
        } else {
            RoleList::doForward($this, $this->_request);
        }
        DB::conn('db');
    }

    public function indexAction() {
        header("content-type:text/html;charset=utf-8");
        $list = UserRoleList::getRoleList();
        ob_end_clean();
        ob_start();
        require_once(LIBRARY_PATH . '/PHPExcel.php');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', 'UID')//作品ID
                ->setCellValue('B1', '用户名')//
                ->setCellValue('C1', '真实姓名')
                ->setCellValue('D1', '权限')
                ->setCellValue('E1', '开通时间');

        for ($a = 0; $a < count($list); $a++) {
            $uinfo = $list[$a];
            $m = $a + 2;
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $m, "" . $uinfo['user_id'])
                    ->setCellValue('B' . $m, "" . $uinfo['user_name'])
                    ->setCellValue('C' . $m, "" . $uinfo['user_realname'])
                    ->setCellValue('D' . $m, "" . $uinfo['acl'])
                    ->setCellValue('E' . $m, "" . $uinfo['user_mdate']);
        }
        $objPHPExcel->getActiveSheet()->setTitle('Sheet1');
        //$objPHPExcel->setActiveSheetIndex(0);
        //ob_start();
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . iconv("UTF-8", "GB2312", "管理后台用户权限list.xlsx") . '"');
        header('Cache-Control: max-age=0');
        header("Content-Transfer-Encoding:binary");
        header("Content-Type:application/download");
        $objWriter->save('php://output');
        exit();
    }

    //查看用户权限
    public function showAction() {
        header("content-type:text/html;charset=utf-8");
        $filter = new Zend_Filter_StripTags();
        $user_id = trim($filter->filter($this->_request->getParam('user_id')));
        if (empty($user_id) && !is_numeric($user_id)) {
            throw new Exception("Error");
        }
        $userinfo = UserList::getUserInfoById($user_id);

        if ($userinfo['role_name'] == '') {
            $userinfo['role_name'] = $userinfo['user_name'] . "_role";
        }
        $this->view->userinfo = $userinfo;
        $this->view->user_id = $user_id;
        if (trim($userinfo['role_menu']) !== '') {
            $this->view->mlist = unserialize($userinfo['role_menu']);
        } else {
            $this->view->mlist = array();
        }

        $acllist = MenuInit::doReflectionMethod('op');
        foreach ($acllist as $key => &$val) {
            if (isset($val['action'])) {
                $n = 0;
                foreach ($val['action'] as $subkey => &$subval) {
                    if (in_array($val['resource'] . '_' . $subval['action'], $this->view->mlist)) {
                        $n = 1;
                    } else {
                        unset($acllist[$key]['action'][$subkey]);
                    }
                }
            }
            if ($n) {
                $val['show'] = 1;
            } else {
                $val['show'] = 0;
                unset($acllist[$key]);
            }
        }
        $this->view->acllist = $acllist;
        $str = '';
        foreach ($acllist as $key => $val) {
            if (trim($val['title']) !== '') {
                $str .=trim($val['title']) . ":";
            } else {
                //$str .=trim($val['resource']) . ":";
            }
            if (isset($val['action'])) {
                $n = 0;
                $str .= "\t";
                foreach ($val['action'] as $subkey => &$subval) {
                    if (in_array($val['resource'] . '_' . $subval['action'], $this->view->mlist)) {
                        if ($n == 0) {
                            if (trim($subval['title']) !== '') {
                                $str .= trim($subval['title']);
                            } else {
                                $str .= trim($subval['action']) . '|';
                            }
                        } else {
                            if (trim($subval['title']) !== '') {
                                $str .= "、" . trim($subval['title']);
                            } else {
                                $str .= "、" . trim($subval['action']);
                            }
                        }
                    }
                    $n++;
                }
            }
            $str .= "\r\n";
        }
        if ($userinfo['role_name'] !== 'admin') {
            echo $str;
        } else {
            echo '系统管理员';
        }
        exit();
    }

}
