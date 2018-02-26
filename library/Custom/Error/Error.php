<?php
class Custom_Error_Error
{
    public function location($url,$message='')
    {
        if(empty($message)) {
            $action = '<script language="JavaScript" type="text/javascript">location.href="'.$url.'";</script>';
        } else {
            $action = '<script language="JavaScript" type="text/javascript">alert("'.$str.'");location.href="'.$url.'";</script>';
        }

        echo ' <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>错误参数，页面跳转</title>
                </head>
                <body>
                '.$action.'
                </body>
                </html>';
       }
       public function history()
       {
           echo ' <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>错误参数，页面跳转。</title>
                </head>
                <body>
                <script language="JavaScript" type="text/javascript">
                alert("错误参数，页面跳转。");
                history.go(-1);
                </script>
                </body>
                </html>';
       }
       
       
}