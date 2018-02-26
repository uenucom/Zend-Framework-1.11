<?php

class Tools extends Zend_Db_Table {

    /**
     * 
     * @param type $key
     * @param type $value
     * @param type $expTime
     * @param type $g_domain
     */
    public static function saveCookie($key, $value, $expTime, $g_domain = '') {
        $expTime = $expTime ? time() + $expTime : '';
        if (!$g_domain) {
            if (stristr($_SERVER['SERVER_NAME'], ".uenu.com")) {
                $g_domain = ".uenu.com";
            }  else if (stristr($_SERVER['SERVER_NAME'], ".uenu.com.cn")) {
                $g_domain = ".uenu.com.cn";
            } else {
                $g_domain = current(explode(':', $_SERVER['SERVER_NAME']));
            }
        }
        setcookie($key, $value, $expTime, "/", $g_domain, FALSE, TRUE);
    }

    /**
     * 生成访客ID
     */
    public static function SetUid() {
        $uid = "";
        $cookie_uid = false;
        if (isset($_COOKIE["MANAGE_WISE_UID"]) && self::CheckUidFormat($_COOKIE["MANAGE_WISE_UID"])) {
            $uid = $_COOKIE["MANAGE_WISE_UID"];
            $cookie_uid = true;
        } else {
            $uid = "wiaui_" . time() . "_" . rand(1000, 9999);
        }
        if (!isset($_COOKIE['MANAGE_WISE_UID']) && $cookie_uid == false && $uid) {
            self::saveCookie('MANAGE_WISE_UID', $uid, 60 * 60 * 24 * 30 * 12 * 25);
        }
        if (isset($_COOKIE['MANAGE_WISE_UID']) && $_COOKIE['MANAGE_WISE_UID'] != $uid) {
            self::saveCookie('MANAGE_WISE_UID', $uid, 60 * 60 * 24 * 30 * 12 * 25);
        }
        return $uid;
    }

    /**
     * 验证访客ID
     * @param string $baidu_wise_uid
     * @return boolean
     */
    public static function CheckUidFormat($baidu_wise_uid) {
        //2012-10-25 增加新uid格式判断 32位字母或数字
        if (preg_match("/^[0-9a-zA-Z]{32}$/", $baidu_wise_uid)) {
            return true;
        }
        if (strlen($baidu_wise_uid) > 0 && stristr($baidu_wise_uid, "_") && preg_match("/^[^\"\'\<\>]+$/", $baidu_wise_uid)) {
            //检查后四位是否为数字
            $len = strlen($baidu_wise_uid);
            if ($len >= 4) {
                $lastFour = substr($baidu_wise_uid, $len - 4);
                if (is_numeric($lastFour)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 记录log
     * @param string $message string $filename
     * @return 
     */
    public static function writeLog($message, $filename = 'managelog') {
        $message_type = 3;
        $date = date("Y-m-d", time());
        $logpath = APPLOG_PATH;
        if (!is_dir($logpath)) {
            @mkdir($logpath, 0777, true);
        }
        if (substr($logpath, -1) != '/') {
            $logpath .= '/';
        }
        $destination = APPLOG_PATH . DIRECTORY_SEPARATOR . "managelog-{$date}.log";
        @error_log("$nowtime,$message\r\n", $message_type, $destination);
    }

    /**
     * 异常处理日志
     *
     * @param object $e
     */
    public static function saveException($e) {
        $cmp = Zend_Version::compareVersion('1.8.2');
        switch ($cmp) {
            case -1:
                $readme = "This Framework is a new version";
                break;
            case 0:
                $readme = "";
                break;
            case 1:
                $readme = "This Framework is a old version";
                break;
        }

        $Ipaddress = new Custom_Controller_Plugin_Ipaddress();
        $IP = $Ipaddress->getIP();
        $Loction = new Custom_Controller_Plugin_IpLocation();
        $Loctiondata = $Loction->getlocation($IP);
        $ReportError = "<fieldset><legend>&nbsp;&nbsp;<font style=\"font-family:georgia\" color=\"red\">Error!</font>&nbsp;&nbsp;</legend>";
        $ReportError .= "<font color = \"green\">Zend framework Version:" . Zend_Version::VERSION . "&nbsp;&nbsp;&nbsp;&nbsp;" . $readme . "&nbsp;&nbsp;&nbsp;&nbsp;";
        $ReportError .= "Time : " . date("Y-m-d H:i:s", time()) . "&nbsp;&nbsp;&nbsp;&nbsp;IP : " . $IP . ' ' . iconv('UTF-8', 'GB2312', $Loctiondata['country'] . $Loctiondata['area']) . "</font><br><br>";
        $ReportError .= nl2br($e->__toString());
        $ReportError .= "<Br />";
        $ReportError .= "http://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . "<Br /><Br />";
        $ReportError .= "<font color=\"green\">Caught exception: " . get_class($e) . "\n</font>";
        $ReportError .= "<br />";
        $ReportError .= "<font color=\"green\">Message: " . $e->getMessage() . "\n</font><br/><br ></fieldset>";
        $ReportError .= "<br >";
        /**/
        $EUrl = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
        $fp = fopen($EUrl . "Error" . date("Y-m-d", time()) . ".html", "a+");
        fwrite($fp, $ReportError);
        fclose($fp);
    }

    //过滤html 中特殊符号
    public static function filterTools($content) {
        $key = array("\"", "'", "`", "+", "-", "/", "\\");
        $result = str_replace($key, "", strip_tags($content));
        return $result;
    }

    //更新缓存文件
    public static function changeCacheFile($cachename) {
        $oldfile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'systemcache' . DIRECTORY_SEPARATOR . $cachename . '.dat';
        $journalfile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'systemcache' . DIRECTORY_SEPARATOR . $cachename . '.dat-journal';
        $tmpname = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'systemcache' . DIRECTORY_SEPARATOR . $cachename . '_tmp.dat';
        if (@unlink($oldfile)) {
            rename($tmpname, $oldfile);
            @unlink($journalfile);
            echo "update $oldfile ok.<br />";
        } else {
            echo 'error:' . $cachename . '<br />';
        }
    }

    //清除原始文件
    public static function delCacheFile($cachename) {
        $oldfile = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'systemcache' . DIRECTORY_SEPARATOR . $cachename . '.dat';
        if (@unlink($oldfile)) {
            echo 'del ok;';
        } else {
            echo 'del fail: no found that file';
        }
    }

    //过滤特殊字符
    public static function setMenuUrl($menuname, $num) {
        $menuname = str_replace(" ", "-", $menuname);
        $lists = array("(", ")", "/", "  ");
        $onlyconsonants = str_replace($lists, " ", $menuname);
    }

    //获取时间

    /**
     * 时间戳转换
     *
     * @param string $string
     * @return string
     */
    public static function dateChangeTime($string) {
        $dateresult = explode('/', $string);
        $date = $dateresult['0'] . '-' . $dateresult['1'] . '-' . $dateresult['2'] . ' 00:00:00.000';
        return $date;
    }

    /**
     * 编码装换
     *
     * @return array
     */
    public static function xmlsoap() {
        $xmldoc = file_get_contents("php://input");
        //$xml = simplexml_load_string($xmldoc);
        $jsonContents = Zend_Json::fromXml($xmldoc, true);
        $xmlarray = Zend_Json::decode($jsonContents);
        return $xmlarray;
    }

    /**
     * 获取系统信息
     *
     * @return array
     */
    public static function getSysInfo() {
        $Ipaddress = new Custom_Controller_Plugin_Ipaddress();
        $IP = $Ipaddress->getIP();
        $domainarray = explode('.', $_SERVER["SERVER_NAME"]);
        $num = count($domainarray);
        $sys = array();
        $sys['ip'] = $IP;
        $sys['host'] = $domainarray[($num - 2)] . '.' . $domainarray[($num - 1)];
        return $sys;
    }

    public static function getCurrentURL() {
        $url = $_SERVER["REQUEST_URI"];
        return $url;
    }

    /**
     * 生成密码
     *
     * @param integer $length
     * @return string
     */
    function generate_password($length = 8) {
        // 密码字符集，可任意添加你需要的字符
        //$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            //$password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $password;
    }

    /**
     * utf-8 转unicode
     *
     * @param string $name
     * @return string
     */
    function utf8_unicode($name) {
        $name = iconv('UTF-8', 'UCS-2', $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            if (ord($c) > 0) { //两个字节的文字
                $str .= base_convert(ord($c), 10, 16) . str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            } else {
                $str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            }
        }
        $str = strtoupper($str);
        return $str;
    }

    /**
     * unicode 转 utf-8
     *
     * @param string $name
     * @return string
     */
    function unicode_decode($name) {
        $name = strtolower($name);
        // 转换编码，将Unicode编码转换成可以浏览的utf-8编码
        $pattern = '/([\w]+)|(\\\u([\w]{4}))/i';
        preg_match_all($pattern, $name, $matches);
        if (!empty($matches)) {
            $name = '';
            for ($j = 0; $j < count($matches[0]); $j++) {
                $str = $matches[0][$j];
                if (strpos($str, '\\u') === 0) {
                    $code = base_convert(substr($str, 2, 2), 16, 10);
                    $code2 = base_convert(substr($str, 4), 16, 10);
                    $c = chr($code) . chr($code2);
                    $c = iconv('UCS-2', 'UTF-8', $c);
                    $name .= $c;
                } else {
                    $name .= $str;
                }
            }
        }
        return $name;
    }

    /**
     * 计算程序执行时间
     *
     * @param  int $st
     * @return float
     */
    public function usedTime($st) {
        $usedtime = microtime() - $st;
        return round($usedtime, 2);
    }

    //计算时间
    public function microtime_float() {
        list($usec, $sec) = explode(" ", microtime());
        $thetime = (float) $usec + (float) $sec;
        return round($thetime, 2);
    }

    /**
     * 清除系统缓存
     *
     * @param string $module
     */
    public function clearSystemCache($module) {
        $cache_dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'zend_cache';
        //echo $cache_dir;
        //放缓存文件的目录
        $this->deleteDir($cache_dir, false, true);
    }

    public function SureRemoveDir($dir, $DeleteMe) {
        if (!$dh = @opendir($dir))
            return;
        while (($obj = readdir($dh))) {
            if ($obj == '.' || $obj == '..')
                continue;
            if (!@unlink($dir . '/' . $obj))
                SureRemoveDir($dir . '/' . $obj, true);
        }
        if ($DeleteMe) {
            closedir($dh);
            @rmdir($dir);
        }
    }

    /**
     * 删除文件方法
     *
     * @param string $Directory
     * @param string $type
     */
    public function deleteDir($Directory, $DeleteMe = false, $show = false) {
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
                if (is_dir($file_path)) {
                    $this->deleteDir($file_path, true, $show);
                } else {
                    if (unlink($file_path)) {
                        if ($show) {
                            echo "删除文件: $file_path 成功！<br/>";
                        }
                    } else {
                        if ($show) {
                            die("删除文件： $file_path 失败！<br/>");
                        }
                    }
                }
            }
        }
        closedir($handle);
        if ($DeleteMe) {
            if (rmdir($Directory)) {
                if ($show) {
                    echo "删除目录： $Directory 成功！<br/>";
                }
            }
        }
    }

    /**
     * 删除文件方法
     *
     * @param string $filename
     */
    public function deleteFile($filename) {
        if (is_file($filename)) {
            @unlink($filename);  //删除文件
            //echo "删除文件$filename";
        }
    }

    /**
     * 验证当前url的安全性
     *
     */
    public function checkCurrentUrl() {
        $lasturl = $this->getLastUrl();
        preg_match("/^(http:\/\/)?([^\/]+)/i", $lasturl, $matches);
        $host = $matches[2];
        if ($host == $_SERVER['HTTP_HOST']) {
            return 1;
        } else {
            echo 'Deny.';
            exit();
        }
    }

    /**
     * 获取post 前的url
     *
     * @return string
     */
    public function getLastUrl() {
        $lasturl = $_SERVER['HTTP_REFERER'];
        return $lasturl;
    }

    //设置flash 访问ip列表
    public function hostAllowIpList() {
        $iplist = '127.0.0.1;192.168.200.13;192.168.200.25';
        return $iplist;
    }

    /**
     * IP检查机制，限制IP访问 hosts.deny hosts.allow
     *
     * @param string $iplist
     * @return int
     */
    public function hostAllow($iplist) {
        $Ipaddress = new Custom_Controller_Plugin_Ipaddress();
        $IP = $Ipaddress->getIP();

        $listarray = explode(";", $iplist);
        $result = 'deny'; //注释
        for ($a = 0; $a < count($listarray); $a++) {
            if ($listarray[$a] != "" && $listarray[$a] == $IP) {
                $result = 1;
            }
        }
        return $result;
    }

    /**
     * 装换数组方法
     *
     * @param string $list
     * @return array
     */
    public function listToArray($list) {
        $listarray = explode(";", $list);
        $result = array();
        for ($a = 0; $a < count($listarray); $a++) {
            if ($listarray[$a] != "") {
                $result[$a] = $listarray[$a];
            }
        }
        return $result;
    }

    /**
     * Fckeditor 编辑器
     *
     * @param int $style
     * @param string $name
     * @param string $content
     * @return object
     */
    public function setFCKeditor($style = '1', $width = '80%', $height = '200', $name, $content = '') {
        $FCKeditor = new Custom_Controller_Plugin_Fckeditor($name);
        if ($style == '1') {
            $FCKeditor->ToolbarSet = 'Default';
        } else {
            $FCKeditor->ToolbarSet = 'Basic'; //Default Basic
        }
        $FCKeditor->Config['ToolbarStartExpanded'] = false; //工具条收缩与展开
        //FCK皮肤
        $FCKeditor->BasePath = "/js/fckeditor/";
        $FCKeditor->Config['SkinPath'] = '/js/fckeditor/editor/skins/default/';
        $FCKeditor->Width = $width; //宽度'80%'
        $FCKeditor->Height = $height; //高度'200'
        $FCKeditor->Value = stripslashes($content);
        //注意:以下代码需要在Controller中输出
        //$this->smarty->assign("FCKeditor",$FCKeditor);
        return $FCKeditor;
    }

    /**
     * Zend_Paginator分页方法
     *
     * @param array $someData
     * @param int $currentPage
     * @return object
     */
    public function setZendPaginatorParameter($someData, $currentPage) {
        Zend_Paginator::setDefaultScrollingStyle(PaginatorDefaultScrollingStyle); // Sliding Elastic Jumping All
        $paginator = Zend_Paginator::factory($someData);
        //==========================   分页缓存  ============================
        /**
          $module = $request->module;
          $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
          $bO = array('cache_dir'=>'../application/modules/'.$module.'/logs/');
          $cache = Zend_cache::factory('Core', 'File', $fO, $bO);
          Zend_Paginator::setCache($cache);
          /* */
        //==================================================================
        $paginator->setItemCountPerPage(PaginatorItemCountPerPage);        //设置每页显示的最大数量（默认是10）
        $paginator->setCurrentPageNumber($currentPage);                    //设置当前页码
        $paginator->setPageRange(PaginatorPageRange);                      //设置页码里显示多少页（默认为10页）
        $result->pages = $paginator->getPages();
        $result->items = $paginator->getIterator();
        return $result;
    }

    /**
     * 设置分页模块(适用于视频comment js分页)
     *
     * @param array $someData
     * @param int $shownum
     * @param int $currentPage
     * @return object
     */
    public static function setZendPaginatorByShowNum($someData, $shownum, $currentPage) {
        Zend_Paginator::setDefaultScrollingStyle(PaginatorDefaultScrollingStyle); // Sliding Elastic Jumping All
        $paginator = Zend_Paginator::factory($someData);
        //==========================   分页缓存  ============================
        /**
          $module = $request->module;
          $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
          $bO = array('cache_dir'=>'../application/modules/'.$module.'/logs/');
          $cache = Zend_cache::factory('Core', 'File', $fO, $bO);
          Zend_Paginator::setCache($cache);
          /* */
        //==================================================================
        $paginator->setItemCountPerPage($shownum);                         //设置每页显示的最大数量（默认是10）
        $paginator->setCurrentPageNumber($currentPage);                    //设置当前页码
        $paginator->setPageRange(PaginatorPageRange);                      //设置页码里显示多少页（默认为10页）
        $result->pages = $paginator->getPages();
        $result->items = $paginator->getIterator();
        return $result;
    }

    /**
     * 新的分页模块
     *
     * @param string $sql
     * @param int $currentPage
     * @return array
     */
    public function setZendPaginatorParameterByDiy($sql, $currentPage) {
        Zend_Paginator::setDefaultScrollingStyle(PaginatorDefaultScrollingStyle); // Sliding Elastic Jumping All
        //$paginator = Zend_Paginator::factory($someData);
        $paginator = new Zend_Paginator(new Custom_Controller_Plugin_Mypager($sql, ($currentPage - 1) * PaginatorItemCountPerPage, PaginatorItemCountPerPage));
        //==========================   分页缓存  ============================
        /**
          $module = $request->module;
          $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
          $bO = array('cache_dir'=>'../application/modules/'.$module.'/logs/');
          $cache = Zend_cache::factory('Core', 'File', $fO, $bO);
          Zend_Paginator::setCache($cache);
          /* */
        //==================================================================
        $paginator->setItemCountPerPage(PaginatorItemCountPerPage);        //设置每页显示的最大数量（默认是10）
        $paginator->setCurrentPageNumber($currentPage);                    //设置当前页码
        $paginator->setPageRange(PaginatorPageRange);                      //设置页码里显示多少页（默认为10页）
        $result->pages = $paginator->getPages();
        $result->items = $paginator->getIterator();
        return $result;
    }

    /**
     * 设置分页模块(适用于视频comment js分页)
     *
     * @param string $sql
     * @param int $shownum
     * @param int $currentPage
     * @return array
     */
    public function setZendPaginatorByShowNumByDiy($sql, $shownum = 15, $currentPage = 1) {
        Zend_Paginator::setDefaultScrollingStyle(PaginatorDefaultScrollingStyle); // Sliding Elastic Jumping All
        //$offset, $itemCountPerPage
        $paginator = new Zend_Paginator(new Custom_Controller_Plugin_Mypager($sql, ($currentPage - 1) * $shownum, $shownum));
        //==========================   分页缓存  ============================
        /**
          $module = $request->module;
          $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
          $bO = array('cache_dir'=>'../application/modules/'.$module.'/logs/');
          $cache = Zend_cache::factory('Core', 'File', $fO, $bO);
          Zend_Paginator::setCache($cache);
          /* */
        //==================================================================
        $paginator->setItemCountPerPage($shownum);                         //设置每页显示的最大数量（默认是10）
        $paginator->setCurrentPageNumber($currentPage);                    //设置当前页码
        $paginator->setPageRange(PaginatorPageRange);                      //设置页码里显示多少页（默认为10页）
        $result->pages = $paginator->getPages();
        $result->items = $paginator->getIterator();
        return $result;
    }

    /**
     * 设置分页模块(适用于mysql 快速分页)
     *
     * @param string $sql
     * @param int $shownum
     * @param int $showpagenum
     * @param int $currentPage
     * @return array
     */
    public static function setZendPaginatorByShowNumByDiyHighPage($sql, $shownum = 20, $showpagenum = PaginatorPageRange, $currentPage = 1) {
        Zend_Paginator::setDefaultScrollingStyle(PaginatorDefaultScrollingStyle); // Sliding Elastic Jumping All
        //$offset, $itemCountPerPage
        $paginator = new Zend_Paginator(new Custom_Controller_Plugin_Mypager($sql, ($currentPage - 1) * $shownum, $shownum));
        //==========================   分页缓存  ============================
        /**
          $module = $request->module;
          $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
          $bO = array('cache_dir'=>'../application/modules/'.$module.'/logs/');
          $cache = Zend_cache::factory('Core', 'File', $fO, $bO);
          Zend_Paginator::setCache($cache);
          /* */
        //==================================================================
        $paginator->setItemCountPerPage($shownum);                         //设置每页显示的最大数量（默认是10）
        $paginator->setCurrentPageNumber($currentPage);                    //设置当前页码
        $paginator->setPageRange($showpagenum);                            //设置页码里显示多少页（默认为10页）
        $result->pages = $paginator->getPages();
        $result->items = $paginator->getIterator();
        return $result;
    }

    /**
     * 设置SQL server 2008 分页模块
     *
     * @param string $dataresult
     * @param string $numsql
     * @param int $shownum
     * @param int $currentPage
     * @return object
     */
    public static function setZendPaginatorByShowNumByDiySQlServer($dataresult, $numsql, $shownum = 15, $currentPage = 1) {
        Zend_Paginator::setDefaultScrollingStyle(PaginatorDefaultScrollingStyle); // Sliding Elastic Jumping All
        //$offset, $itemCountPerPage
        $paginator = new Zend_Paginator(new Custom_Controller_Plugin_Myotherpager($dataresult, $numsql, ($currentPage - 1) * $shownum, $shownum));
        //==========================   分页缓存  ============================
        /**
          $module = $request->module;
          $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
          $bO = array('cache_dir'=>'../application/modules/'.$module.'/logs/');
          $cache = Zend_cache::factory('Core', 'File', $fO, $bO);
          Zend_Paginator::setCache($cache);
          /* */
        //==================================================================
        $paginator->setItemCountPerPage($shownum);                         //设置每页显示的最大数量（默认是10）
        $paginator->setCurrentPageNumber($currentPage);                    //设置当前页码
        $paginator->setPageRange(PaginatorPageRange);                      //设置页码里显示多少页（默认为10页）
        $result->pages = $paginator->getPages();
        $result->items = $paginator->getIterator();
        return $result;
    }

    /**
     * 设置SQL server 2008 分页模块
     *
     * @param string $dataresult
     * @param string $numsql
     * @param int $shownum
     * @param int $currentPage
     * @return object
     */
    public static function setZendPaginatorByShowNumByDiySQlServerDefault($dataresult, $numsql, $shownum = 15, $showrange = 10, $currentPage = 1) {
        Zend_Paginator::setDefaultScrollingStyle(PaginatorDefaultScrollingStyle); // Sliding Elastic Jumping All
        //$offset, $itemCountPerPage
        $paginator = new Zend_Paginator(new Custom_Controller_Plugin_Myotherpager($dataresult, $numsql, ($currentPage - 1) * $shownum, $shownum));
        //==========================   分页缓存  ============================
        /**
          $module = $request->module;
          $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
          $bO = array('cache_dir'=>'../application/modules/'.$module.'/logs/');
          $cache = Zend_cache::factory('Core', 'File', $fO, $bO);
          Zend_Paginator::setCache($cache);
          /* */
        //==================================================================
        $paginator->setItemCountPerPage($shownum);                         //设置每页显示的最大数量（默认是10）
        $paginator->setCurrentPageNumber($currentPage);                    //设置当前页码
        $paginator->setPageRange($showrange);                      //设置页码里显示多少页（默认为10页）
        $result->pages = $paginator->getPages();
        $result->items = $paginator->getIterator();
        return $result;
    }

    /**
     * 纯分页类
     *
     * @param array $dataresult
     * @param int   $totalnum
     * @param int   $shownum
     * @param int   $showrange
     * @param int   $currentPage
     * @return object
     */
    public static function setZendPaginatorByShowNumByOnlyPage($dataresult, $totalnum, $shownum = 10, $showrange = 10, $currentPage = 1) {
        Zend_Paginator::setDefaultScrollingStyle(PaginatorDefaultScrollingStyle); // Sliding Elastic Jumping All
        //$offset, $itemCountPerPage
        $paginator = new Zend_Paginator(new Custom_Controller_Plugin_Onlypager($dataresult, $totalnum, ($currentPage - 1) * $shownum, $shownum));
        //==========================   分页缓存  ============================
        /**
          $module = $request->module;
          $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
          $bO = array('cache_dir'=>'../application/modules/'.$module.'/logs/');
          $cache = Zend_cache::factory('Core', 'File', $fO, $bO);
          Zend_Paginator::setCache($cache);
          /* */
        //==================================================================
        $paginator->setItemCountPerPage($shownum);                         //设置每页显示的最大数量（默认是10）
        $paginator->setCurrentPageNumber($currentPage);                    //设置当前页码
        $paginator->setPageRange($showrange);                      //设置页码里显示多少页（默认为10页）
        $result->pages = $paginator->getPages();
        $result->items = $paginator->getIterator();
        return $result;
    }

    /**
     * 设置分页模块(适用于视频comment js分页)
     *
     * @param array $someData
     * @param int $shownum
     * @param int $showrange
     * @param int $currentPage
     * @return object
     */
    public static function setZendPaginatorByShowNumDefault($someData, $shownum, $showrange = 10, $currentPage) {
        Zend_Paginator::setDefaultScrollingStyle(PaginatorDefaultScrollingStyle); // Sliding Elastic Jumping All
        $paginator = Zend_Paginator::factory($someData);
        //==========================   分页缓存  ============================
        /**
          $module = $request->module;
          $fO = array('lifetime' => 3600, 'automatic_serialization' => true);
          $bO = array('cache_dir'=>'../application/modules/'.$module.'/logs/');
          $cache = Zend_cache::factory('Core', 'File', $fO, $bO);
          Zend_Paginator::setCache($cache);
          /* */
        //==================================================================
        $paginator->setItemCountPerPage($shownum);                         //设置每页显示的最大数量（默认是10）
        $paginator->setCurrentPageNumber($currentPage);                    //设置当前页码
        $paginator->setPageRange($showrange);                              //设置页码里显示多少页（默认为10页）
        $result->pages = $paginator->getPages();
        $result->items = $paginator->getIterator();
        return $result;
    }

    /**
     * ip截取方法
     *
     * @param string $ip
     * @return string
     */
    public function truncateIP($ip) {
        $iparray = explode('.', $ip);
        $ipstring = $iparray['0'] . '.' . $iparray['1'] . '.' . $iparray['2'] . '.*';
        return $ipstring;
    }

    /**
     * 没有重复值视频guid
     *
     * @param string $guid
     * @return string
     */
    public function uniqueGuid($guid) {
        $videoarray = explode(";", $guid);
        $guidarray = array_unique($videoarray);
        $videolist = "";
        for ($a = 0; $a < count($videoarray); $a++) {
            if ($guidarray[$a] != "") {
                $videolist .= $guidarray[$a] . ';';
            }
        }
        return $videolist;
    }

    /**
     * 字符长度计算方法
     *
     * @param string $str
     * @return int
     */
    function str_length($str) {
        $len = strlen($str);
        $i = 0;
        while ($i < $len) {
            if (preg_match("/^[" . chr(0xa1) . "-" . chr(0xff) . "]+$/", $str[$i])) {
                $i+=2;
            } else {
                $i+=1;
            }
        }
        return $i;
    }

    /**
     * 中、英文混排字符截取
     *
     * @param string $string //输入字符串
     * @param int    $length //截取字符长度
     * @param string $code   //字符编码
     * @param string $etc    //截取字符结尾标示符
     * @return string
     */
    public function truncatecn($string, $length = 80, $code = 'UTF-8', $etc = '...') {
        //过滤html编码, 去掉空格
        //$string  = trim(strip_tags($string));
        if (strtoupper($code) == 'UTF-8') {
            $result = '';
            $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
            $strlen = strlen($string);

            for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
                if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0')) {
                    if ($length < 1.0) {
                        break;
                    }

                    $result .= substr($string, $i, $number);
                    $length -= 1.0;
                    $i += $number - 1;
                } else {
                    $result .= substr($string, $i, 1);
                    $length -= 0.5;
                }
            }
            $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');

            if ($i < $strlen) {
                $result .= $etc;
            }
            return $result;
        } elseif (strtoupper($code) == 'GB2312') {
            $result = '';
            $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'GB2312');
            $strlen = strlen($string);

            for ($i = 0; (($i < $strlen) && ($length > 0)); $i++) {
                if (ord(substr($string, $i, 1)) > 128) {
                    if ($length < 1.0) {
                        break;
                    }
                    $result .= substr($string, $i, 2);
                    $length -= 1.0;
                    $i++;
                } else {
                    $result .= substr($string, $i, 1);
                    $length -= 0.5;
                }
            }
            $result = htmlspecialchars($result, ENT_QUOTES, 'GB2312');
            if ($i < $strlen) {
                $result .= $etc;
            }
            return $result;
        } else {
            if ($length == 0)
                return '';
            if ($code == 'UTF-8') {
                $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            } else {
                $pa = "/[\x01-\x7f]|[\xa1-\xff][\xa1-\xff]/";
            }
            preg_match_all($pa, $string, $t_string);
            if (count($t_string[0]) > $length) {
                return join('', array_slice($t_string[0], 0, $length)) . $etc;
            }
            return join('', array_slice($t_string[0], 0, $length));
        }
    }

    /**
     * 根据检索关键字 获取视频
     *
     * @param string $wd
     * @param int $rows
     * @param int $str_length
     * @return array
     */
    public function getSearchAutoComplete($wd, $rows = 10, $str_length = 20) {
        $DB = Zend_Registry::get('dbAdapter');
        $select = $DB->select()
                ->from('video_lists', '*')
                ->where('list_publish = ?', '1')
                ->where('list_title like ?', '%' . $wd . '%')
                //->orwhere('list_label like ?', '%'.$wd.'%')
                //->orwhere('list_intro like ?', '%'.$wd.'%')
                ->order('list_id  DESC')
                ->limit($rows, 0);
        $videoresult = $DB->fetchAll($select->__toString());

        $result = array();
        foreach ($videoresult as $arr) {
            $n = $arr['list_title'];
            if ($this->str_length($n) > ($str_length * 2)) {
                //$n = $this -> getstr($n, 41, 0, 0, 0, 0, -1);
                $n = $this->truncatecn($n, $str_length, 'UTF-8', '');
                $result[$n] = $n;
            } else {
                $result[$n] = $n;
            }
        }
        return $result;
    }

    /**
     * 根据检索关键字 获取视频
     *
     * @param int    $type
     * @param string $wd
     * @param int    $rows
     * @param int    $str_length
     * @return array
     */
    public function getSearchAutoCompleteByType($type = 1, $wd, $rows = 10, $str_length = 20) {
        $DB = Zend_Registry::get('dbAdapter');
        if ($type == 1) {
            $select = $DB->select()
                    ->from('video_lists', '*')
                    ->where('list_publish = ?', '1')
                    ->where('list_title like ?', '%' . $wd . '%')
                    //->orwhere('list_label like ?', '%'.$wd.'%')
                    //->orwhere('list_intro like ?', '%'.$wd.'%')
                    ->order('list_id  DESC')
                    ->limit($rows, 0);
            $videoresult = $DB->fetchAll($select->__toString());

            $result = array();
            foreach ($videoresult as $arr) {
                $n = $arr['list_title'];
                if ($this->str_length($n) > ($str_length * 2)) {
                    //$n = $this -> getstr($n, 41, 0, 0, 0, 0, -1);
                    $n = $this->truncatecn($n, $str_length, 'UTF-8', '');
                    $result[$n] = $n;
                } else {
                    $result[$n] = $n;
                }
            }
        } elseif ($type == 2) {
            $select = $DB->select()
                    ->from('video_lists', '*')
                    ->where('list_lecture like ?', '%' . $wd . '%')
                    ->group('list_lecture')
                    ->limit($rows, 0);
            //echo $select ->__toString();exit();
            $videoresult = $DB->fetchAll($select->__toString());
            $result = array();
            foreach ($videoresult as $arr) {
                $n = $arr['list_lecture'];
                if ($this->str_length($n) > ($str_length * 2)) {
                    //$n = $this -> getstr($n, 41, 0, 0, 0, 0, -1);
                    $n = $this->truncatecn($n, $str_length, 'UTF-8', '');
                    $result[$n] = $n;
                } else {
                    $result[$n] = $n;
                }
            }
        } elseif ($type == 3) {

            $select = $DB->select()
                    ->from('video_lists', '*')
                    ->where('list_lecture_address like ?', '%' . $wd . '%')
                    ->group('list_lecture_address')
                    ->limit($rows, 0);
            //echo $select ->__toString();exit();
            $videoresult = $DB->fetchAll($select->__toString());
            $result = array();
            foreach ($videoresult as $arr) {
                $n = $arr['list_lecture_address'];
                if ($this->str_length($n) > ($str_length * 2)) {
                    //$n = $this -> getstr($n, 41, 0, 0, 0, 0, -1);
                    $n = $this->truncatecn($n, $str_length, 'UTF-8', '');
                    $result[$n] = $n;
                } else {
                    $result[$n] = $n;
                }
            }
        }
        return $result;
    }

    /**
     * 以附件形式下载文件
     *
     * @param string $fileDownloadPath
     */
    public function downloadFile($fileName) {
        $fileDownloadPath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'htdocs' . DIRECTORY_SEPARATOR . 'download' . DIRECTORY_SEPARATOR . $fileName;
        $file = fopen($fileDownloadPath, "r");
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        header("Cache-Control: no-store, no-cache");
        Header("Accept-Length: " . (string) filesize($fileDownloadPath));
        Header("Content-Disposition: attachment; filename=" . basename(iconv("UTF-8", "GB2312", $fileName))); //支持中文名文件下载
        header("Content-Transfer-Encoding: binary\n");
        echo fread($file, filesize($fileDownloadPath));
        fclose($file);
    }

    /**
     * 下载远程的文件保存在本地
     *
     * @param string $fFileHTTPPath
     * @param string $fFileSavePath
     * @param string $fFileSaveName
     * @return int
     */
    public function saveHttpFile($fFileHTTPPath, $fFileSavePath, $fFileSaveName) {
        //记录程序开始的时间
        $BeginTime = $this->getmicrotime();
        if ($fFileSavePath == '') {
            $fFileSavePath = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'htdocs' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'saveHttpFile' . DIRECTORY_SEPARATOR; //放缓存文件的目录
        }
        //取得文件名
        $fFileSaveName = $fFileSavePath . $fFileSaveName;

        //取得文件的内容
        ob_start();
        readfile($fFileHTTPPath);
        $img = ob_get_contents();
        ob_end_clean();
        //$size = strlen($img);
        //保存到本地
        $fp2 = @fopen($fFileSaveName, "a");
        fwrite($fp2, $img);
        fclose($fp2);
        //记录程序运行结束的时间
        $EndTime = $this->getmicrotime();
        //返回运行时间
        return($EndTime - $BeginTime);
    }

    /**
     * 获取微秒值
     *
     * @return float
     */
    public function getmicrotime() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * 评论时间算法 秒 小时 天 月 年
     *
     * @param int $time
     * @return string
     */
    public function changeRecentTimeType($time) {
        $returntime = '';
        $timecmpresult = time() - $time + 1;
        if ($timecmpresult >= 0 && $timecmpresult < 60) {
            $returntime = $timecmpresult . '秒前';
        } elseif ($timecmpresult >= 60 && $timecmpresult < 3600) {
            $returntime = floor($timecmpresult / 60) . '分钟前';
        } elseif ($timecmpresult >= 3600 && $timecmpresult < 3600 * 24) {
            $returntime = floor($timecmpresult / 3600) . '小时前';
        } elseif ($timecmpresult >= 3600 * 24 && $timecmpresult < 3600 * 24 * 30) {
            $returntime = floor($timecmpresult / (3600 * 24)) . '天前';
        } elseif ($timecmpresult >= 3600 * 24 * 30 && $timecmpresult < 3600 * 24 * 30 * 12) {
            $returntime = floor($timecmpresult / (3600 * 24 * 30)) . '个月前';
        } elseif ($timecmpresult >= 3600 * 24 * 30 * 12) {
            $returntime = floor($timecmpresult / (3600 * 24 * 30 * 12)) . '年前';
        } else {
            $returntime = '错误的时间';
        }
        return $returntime;
    }

    /**
     * 进制转换方法
     *
     * @param string $data
     * @param int $in
     * @param int $out
     * @param int $num
     * @return string
     */
    public function jinzhichange($data, $in, $out, $num) {
        //$hexadecimal = 'A37334';
        //echo base_convert($hexadecimal, 16, 2);
        $input = base_convert($data, $in, $out);
        if ($num != '') {
            $result = str_pad($input, $num, "0", STR_PAD_LEFT);
        } else {
            $result = $input;
        }
        return $result;
    }

    //=================================  以下是临时方法 参考uchome     ===========================================
    /**
     * 获取字符串
     *
     * @param string $string
     * @param int $length
     * @param int $in_slashes
     * @param int $out_slashes
     * @param int $censor
     * @param int $bbcode
     * @param int $html
     * @return string
     */
    public function getstr($string, $length, $in_slashes = 0, $out_slashes = 0, $censor = 0, $bbcode = 0, $html = 0) {
        global $_SC, $_SGLOBAL;

        $string = trim($string);

        if ($in_slashes) {
            //传入的字符有slashes
            $string = sstripslashes($string);
        }
        if ($html < 0) {
            //去掉html标签
            $string = preg_replace("/(\<[^\<]*\>|\r|\n|\s|\[.+?\])/is", ' ', $string);
            $string = shtmlspecialchars($string);
//            $string = strip_tags($string);
        } elseif ($html == 0) {
            //转换html标签
            //$string = shtmlspecialchars($string);
            $string = strip_tags($string);
        }
        if ($censor) {
            //词语屏蔽
            //@include_once(S_ROOT.'./data/data_censor.php');
            if ($_SGLOBAL['censor']['banned'] && preg_match($_SGLOBAL['censor']['banned'], $string)) {
                showmessage('information_contains_the_shielding_text');
            } else {
                $string = empty($_SGLOBAL['censor']['filter']) ? $string :
                        @preg_replace($_SGLOBAL['censor']['filter']['find'], $_SGLOBAL['censor']['filter']['replace'], $string);
            }
        }
        if ($length && strlen($string) > $length) {
            //截断字符
            $wordscut = '';
            if (strtolower($_SC['charset']) == 'utf-8') {
                //utf8编码
                $n = 0;
                $tn = 0;
                $noc = 0;
                while ($n < strlen($string)) {
                    $t = ord($string[$n]);
                    if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                        $tn = 1;
                        $n++;
                        $noc++;
                    } elseif (194 <= $t && $t <= 223) {
                        $tn = 2;
                        $n += 2;
                        $noc += 2;
                    } elseif (224 <= $t && $t < 239) {
                        $tn = 3;
                        $n += 3;
                        $noc += 2;
                    } elseif (240 <= $t && $t <= 247) {
                        $tn = 4;
                        $n += 4;
                        $noc += 2;
                    } elseif (248 <= $t && $t <= 251) {
                        $tn = 5;
                        $n += 5;
                        $noc += 2;
                    } elseif ($t == 252 || $t == 253) {
                        $tn = 6;
                        $n += 6;
                        $noc += 2;
                    } else {
                        $n++;
                    }
                    if ($noc >= $length) {
                        break;
                    }
                }
                if ($noc > $length) {
                    $n -= $tn;
                }
                $wordscut = substr($string, 0, $n);
            } else {
                for ($i = 0; $i < $length - 1; $i++) {
                    if (ord($string[$i]) > 127) {
                        $wordscut .= $string[$i] . $string[$i + 1];
                        $i++;
                    } else {
                        $wordscut .= $string[$i];
                    }
                }
            }
            $string = $wordscut;
        }
        /**
          if($bbcode) {
          include_once(S_ROOT.'./source/function_bbcode.php');
          $string = bbcode($string, $bbcode);
          }
          /* */
        if ($out_slashes) {
            $string = saddslashes($string);
        }
        return trim($string);
    }

    function httpPost($url, $data, $timeout = 2) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }

}
