<?php
class Custom_Error_Direct
{
    public function load($title, $message, $directUrl)
    {
        //$this->smarty->assign("username", $this->auth->username);
        //$this->smarty->display('upload.html');
       
        if($title ==""){$title ='操作完成！';}
        if($message ==""){$title ='提示信息：操作完成！';}
        if($directUrl == ''){
            $jscode  ='<script language="JavaScript" type="text/javascript">
            function direct()
            {
            history.go(-1);
            }
            setTimeout(direct, 3000);</script>';
            $textHref = '跳转';
        }else{
            $reFresh ='<meta http-equiv="refresh" content="3; url='.$directUrl.'" />';
            $textHref = '<a href="'.$directUrl.'">跳转</a>';
        }
        
        $html  ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
'.$reFresh.'
<title>'.$title.'</title>
<style type="text/css">
<!--
.outside {height: 278px;width: 600px; border:1px solid #DCE3EF; margin:100px auto 0 auto;}
.top{ height:26px; width:100%; background-color:#F9F9F9; font-size:14px; font-weight:bold; color:#555; text-align:center; padding:10px 0 0 0; border-bottom:1px solid #DCE3EF;}
.middle{ height:160px; width:95%; font-size:13px; color: #008000; font-weight:bold;  border-bottom:1px dashed #ddd; margin:20px auto 20px auto;}
.footer{ height:100%; width:100%; font-size:12px; color: #666; padding:0 0 0 20px;}
a:link {font-size: 12px;color: #666;}
a:visited {	font-size: 12px;color: #666;}
a:hover {font-size: 12px;color: #06F;}
-->
</style>
</head>

<body>
<div class="outside">
<div class="top">'.$title.'</div>
<div class="middle">'.$message.'</div>
<div class="footer">等待 3 秒后自动'.$textHref.'</div>
</div>
'.$jscode.'
</body>
</html>';
        return $html;
    }
}