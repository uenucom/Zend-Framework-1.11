<?php



class MenuInit {

    /**
     * 菜单
     * @return array
     */
    public static function getList() {
        $data = self::getAllList();
        $newdata = array();
        for ($a = 0; $a < count($data); $a++) {
            $c_data = $data[$a];
            if ($a > 0) {
                $newdata = array_merge_recursive_distinct($newdata, $c_data);
            } else {
                $newdata = $c_data;
            }
        }
        array_multisort($newdata, SORT_ASC);
        return $newdata;
    }

    /**
     * 获取配置参数
     * @param array $data
     * @return array
     */
    public static function getData($data) {
        return $data;
    }

    /**
     * 处理菜单
     * @param string $Directory
     * @return array
     */
    public static function getAllList($Directory = '') {
        $Directory = realpath(APPLICATION_PATH . '/modules');
        $menulist = MenuInit::getModeulList($Directory, true);
        $menu_arr = array();
        for ($a = 0; $a < count($menulist); $a++) {
            $module = $menulist[$a];
            $menu_file = APPLICATION_PATH . "/modules/{$module}/configs/" . APPLICATION_ENV . "/MenuConfig.php";
            if (is_file($menu_file)) {
                $arr = self::getData(require $menu_file);
                if (!empty($arr)) {
                    $menu_arr[] = $arr;
                }
            }
        }
        return $menu_arr;
    }

    public static function do2Act($allmenu, $own_act) {
        if (is_array($allmenu)) {
            foreach ($allmenu as $k => $v) {
                if ($allmenu[$k]['sublist'])
                    $allmenu[$k] = self::do2Act($allmenu[$k], $own_act);
            }
        } else {
            $allmenu = str_replace(array('<', '>'), array('{', '}'), $array);
        }
        return $allmenu;
    }

    /**
     *
     * @param string $str
     * @return string
     */
    public static function doDocText($str) {
        return str_replace(array("/", "*", "#", " ", "\n", "\r"), '', $str);
    }

    /**
     * Reflection
     * @return array
     */
    public static function doReflectionMethod($rolename = '') {
        $Directory = realpath(APPLICATION_PATH . '/modules');
        $menulist = MenuInit::getRoleList($Directory, true);
        $listresult = array();
        if (in_array($rolename, array("admin", "op"))) {
            $filter = array('Member', 'Error', 'Index');
            $Actionfilter = array('Member', 'Error', 'Index');
        } else {
            $filter = array('Member', 'Error', 'Index', 'Role', 'Alog', 'Userexcel', 'Session');
            $Actionfilter = array('role', 'del', 'excel', 'update', 'add', 'index', 'show');
        }

        for ($a = 0; $a < count($menulist); $a++) {
            $filepath = $menulist[$a];
            $list = str_replace($Directory . DIRECTORY_SEPARATOR, '', $menulist[$a]);
            $newlist = explode(DIRECTORY_SEPARATOR, $list);
            $newlist[2] = str_replace("Controller.php" . "", '', $newlist[2]);
            $filecontent = MenuInit::getListByReadFile($filepath);
            $str = '';
            if (!($newlist[0] == 'default' && in_array($newlist['2'], $filter))) {
                if ($newlist[0] !== 'default') {
                    $classname = ucfirst($newlist[0]) . "_" . $newlist[2] . 'Controller';
                } else {
                    $classname = ucfirst($newlist[2]) . 'Controller';
                }
                $filename = APPLICATION_PATH . "/modules/{$newlist[0]}/configs/" . APPLICATION_ENV . "/menu.ini";
                $acl_menu = new Zend_Config_Ini($filename, 'acllist');
                $str = $newlist[0] . "_" . $newlist[2];
                $sub_listresult = array();
                $sub_listresult['resource'] = strtolower($str);
                $sub_listresult['title'] = $acl_menu->$sub_listresult['resource'];
                $str_action = '  [';
                $action_arr = array();
                for ($c = 0; $c < count($filecontent[1]); $c++) {
                    if ($c) {
                        $str_action .= "、" . $filecontent[1][$c];
                    } else {
                        $str_action .= $filecontent[1][$c];
                    }
                    $str_arr['action'] = trim(strtolower($filecontent[1][$c]));
                    $nkey = $sub_listresult['resource'] . "_" . $filecontent[1][$c];
                    $m_doc = $acl_menu->$nkey;
                    $str_arr['title'] = "" . $m_doc;
                    $action_arr[] = $str_arr;
                }
                $str_action .=']';
                if (in_array($sub_listresult['resource'], array('default_user')) && !in_array($rolename, array("admin", "op"))) {
                    $action_arr = array(array('action' => "passwd", "title" => "修改密码"));
                }
                $sub_listresult['action'] = $action_arr;

                $listresult[] = $sub_listresult;
            }
        }
        return $listresult;
    }

    /**
     * 处理菜单
     * @param type $allmenu
     * @param type $own_act
     * @return type
     */
    public static function doAct($allmenu, $own_act) {
        $auth = Zend_Auth::getInstance()->getIdentity();
        if ($auth->role_name === 'admin') {
            return $allmenu;
        } else {
            $keylist = array();
            if (is_array($allmenu)) {
                foreach ($allmenu as $key => $val) {
                    if (isset($val['sublist'])) {
                        if (is_array($val['sublist']) && !empty($val['sublist'])) {
                            $c = 0;
                            foreach ($val['sublist'] as $subkey => $subval) {
                                if (isset($subval['sublist'])) {
                                    if (is_array($subval['sublist']) && !empty($subval['sublist'])) {
                                        $b = 0;
                                        $blist = array();
                                        foreach ($subval['sublist'] as $sub2key => $sub2val) {
                                            if (isset($sub2val['sublist'])) {
                                                if (is_array($sub2val['sublist']) && !empty($sub2val['sublist'])) {
                                                    $a = 0;
                                                    $alist = array();
                                                    foreach ($sub2val['sublist'] as $sub3key => $sub3val) {
                                                        if (isset($sub3val['act'])) {
                                                            if (in_array($sub3val['act'], $own_act)) {
                                                                $a++;
                                                                $b++;
                                                                $c++;
                                                            } else {
                                                                unset($allmenu[$key]['sublist'][$subkey]['sublist'][$sub2key]['sublist'][$sub3key]);
                                                            }
                                                        } else {
                                                            unset($allmenu[$key]['sublist'][$subkey]['sublist'][$sub2key]['sublist'][$sub3key]);
                                                        }
                                                    }
                                                    if (!$a) {
                                                        unset($allmenu[$key]['sublist'][$subkey]['sublist'][$sub2key]);
                                                    } else {
                                                        //for ($aa = 0; $aa < count($alist); $aa++) {
                                                        //$keylist[] = $alist[$aa];
                                                        //}
                                                    }
                                                }
                                            } else {
                                                if (isset($sub2val['act']) && in_array($sub2val['act'], $own_act)) {
                                                    
                                                } else {
                                                    unset($allmenu[$key]['sublist'][$subkey]['sublist'][$sub2key]);
                                                }
                                            }
                                            if (isset($sub2val['act'])) {
                                                if (in_array($sub2val['act'], $own_act)) {
                                                    $b++;
                                                    $c++;
                                                } else {
                                                    $blist[] = "[$key]['sublist'][$subkey]['sublist'][$sub2key]";
                                                }
                                            } else {
                                                $blist[] = "[$key]['sublist'][$subkey]['sublist'][$sub2key]";
                                            }
                                        }

                                        if (!$b) {
                                            unset($allmenu[$key]['sublist'][$subkey]);
                                        }
                                    }
                                }
                                if (isset($subval['act'])) {
                                    if (in_array($subval['act'], $own_act)) {
                                        $c++;
                                    } else {
                                        unset($allmenu[$key]['sublist'][$subkey]);
                                    }
                                }
                            }
                            if (!$c) {
                                unset($allmenu[$key]);
                            }
                        }
                    }
                }
            }
            return $allmenu;
        }
    }

    //获取KEY
    public static function search_act($val, $list) {
        if (is_array($list)) {
            
        }
    }

    public static function getListByReadFile($filepath) {

        $filecontent = file_get_contents($filepath);
        preg_match_all("|function\s(.*)Action|U", $filecontent, $out);
        //print_r($out);
        return $out;
    }

    //反序列化
    public static function unserialize_act($str) {
        if (trim($str) !== '') {
            return unserialize($str);
        } else {
            return array();
        }
    }

    //列出相关文件
    public static function getRoleList($Directory, $show) {
        $list = array();
        if ($show) {
            $error = "目录：  $Directory 不存在<br/>";
        } else {
            $error = '';
        }
        is_dir($Directory) or die($error);
        $handle = openDir($Directory);
        while (($file_name = readdir($handle)) !== false) {
            $file_path = $Directory . DIRECTORY_SEPARATOR . $file_name;
            if ($file_name != "." && $file_name != ".." && $file_name != ".svn") {
                $file_path = realpath($file_path . '/controllers');
                if (is_dir($file_path)) {
                    $subhandle = openDir($file_path);
                    while (($subfile_name = readdir($subhandle)) !== false) { 
                        if ($subfile_name != "." && $subfile_name != ".." && $subfile_name != ".svn") {
                            $subfile_name = $file_path . DIRECTORY_SEPARATOR . $subfile_name;
                            if (preg_match("/Controller\.php$/", $subfile_name)) {
                                $list[] = $subfile_name;
                            }
                        }
                    }
                } else {
                    if (strstr($file_path, "Controller.php")) {
                        $list .="|" . $file_path;
                    }
                }
            }
        }
        closedir($handle);
        return $list;
    }

    //获取模块
    public static function getModeulList($Directory, $show) {
        $list = array();
        if ($show) {
            $error = "目录：  $Directory 不存在<br/>";
        } else {
            $error = '';
        }
        is_dir($Directory) or die($error);
        $handle = openDir($Directory);
        while (($file_name = readdir($handle)) !== false) {
            $file_path = $Directory . DIRECTORY_SEPARATOR . $file_name;
            if ($file_name != "." && $file_name != ".." && $file_name != ".svn") {
                $file_path = realpath($file_path);
                if (is_dir($file_path)) {
                    $dir_name = basename($file_path);
                    if (trim($dir_name) !== '') {
                        $list[] = $dir_name;
                    }
                }
            }
        }
        closedir($handle);
        return $list;
    }

    /**
     * Reflection
     * @return array
     */
    public static function doReflectionMethodbak($rolename = '') {
        $Directory = realpath(APPLICATION_PATH . '/modules');
        $menulist = MenuInit::getRoleList($Directory, true);
        $listresult = array();

        $filename = APPLICATION_PATH . '/configs/menu.ini';
        $acl_menu = new Zend_Config_Ini($filename, 'acllist');
        if (in_array($rolename, array("admin", "op"))) {
            $filter = array('Member', 'Error', 'Index');
            $Actionfilter = array('Member', 'Error', 'Index');
        } else {
            $filter = array('Member', 'Error', 'Index', 'Role', 'Alog', 'Userexcel');
            $Actionfilter = array('role', 'del', 'excel', 'update', 'add', 'index', 'show');
        }

        for ($a = 0; $a < count($menulist); $a++) {
            $filepath = $menulist[$a];
            $list = str_replace($Directory . DIRECTORY_SEPARATOR, '', $menulist[$a]);
            $newlist = explode(DIRECTORY_SEPARATOR, $list);
            $newlist[2] = str_replace("Controller.php" . "", '', $newlist[2]);
            $filecontent = MenuInit::getListByReadFile($filepath);
            $str = '';
            if (!($newlist[0] == 'default' && in_array($newlist['2'], $filter))) {
                //                if ($newlist[0] !== 'default') {
                //                    //$method = new ReflectionMethod($newlist[2] . 'Controller', $filecontent[1][$c] . 'Action');
                //                    $robj = new ReflectionClass($newlist[0] . "_" . $newlist[2] . 'Controller');
                //                } else {
                //                    $robj = new ReflectionClass($newlist[2] . 'Controller');
                //                }
                if ($newlist[0] !== 'default') {
                    //$method = new ReflectionMethod($newlist[2] . 'Controller', $filecontent[1][$c] . 'Action');
                    $classname = ucfirst($newlist[0]) . "_" . $newlist[2] . 'Controller';
                } else {
                    $classname = ucfirst($newlist[2]) . 'Controller';
                }
                require_once $filepath;
                $robj = new ReflectionClass($classname);
                $str = $newlist[0] . "_" . $newlist[2];
                $sub_listresult = array();
                $sub_listresult['resource'] = strtolower($str); //小写
                $sub_listresult['title'] = self::doDocText($robj->getDocComment());
                if (trim($sub_listresult['title']) === '') {
                    $sub_listresult['title'] = $acl_menu->$sub_listresult['resource'];
                }
                $str_action = '  [';
                $action_arr = array();
                for ($c = 0; $c < count($filecontent[1]); $c++) {
                    if ($c) {
                        $str_action .= "、" . $filecontent[1][$c];
                    } else {
                        $str_action .= $filecontent[1][$c];
                    }
                    $str_arr['action'] = strtolower($filecontent[1][$c]);
                    $m_doc = self::doDocText($robj->getMethod($filecontent[1][$c] . 'Action')->getDocComment());
                    if (trim($m_doc) === '') {
                        $nkey = $sub_listresult['resource'] . "_" . $filecontent[1][$c];
                        $m_doc = $acl_menu->$nkey;
                    }
                    $str_arr['title'] = "" . $m_doc;
                    $action_arr[] = $str_arr;
                }
                unset($robj);
                $str_action .=']';

                if (in_array($sub_listresult['resource'], array('default_user')) && !in_array($rolename, array("admin", "op"))) {
                    $action_arr = array(array('action' => "passwd", "title" => "修改密码"));
                }
                $sub_listresult['action'] = $action_arr;

                $listresult[] = $sub_listresult;
            }
        }
        return $listresult;
    }

}
