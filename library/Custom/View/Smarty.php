<?php
require_once 'Smarty/Smarty.class.php';
class Custom_View_Smarty extends Smarty
{

    public function __construct($extraParams = array())
    {
        $this->Smarty();
        foreach ($extraParams as $key => $value){
            $this->$key = $value;
        }
    }

    public function setBasePath($path)
    {
        //const DIR_SEP = DIRECTORY_SEPARATOR;// 路径分割 win下\ linux下/
        //$path = rtrim($path, '/\\').DIRECTORY_SEPARATOR;
        /**
       //DIRECTORY_SEPARATOR：路径分隔符，linux上就是’/’    windows上是’\’
       //PATH_SEPARATOR：include多个路径使用，在win下，当你要include多个路径的话，你要用”;”隔开，但在linux下就使用”:”隔开的。
       /**/
        //$path = rtrim($path, '/\\');
        //$this->template_dir = $path . 'views';
        //$this->template_dir = $path .'/' . 'views'.'/scripts/';
        //echo $path;
        $path = rtrim($path, '/\\');//200908统一采用/为目录，URL格式
        $this->template_dir = $path .'/views/';//模板目录
        //$this->compile_dir  = $path .'/temp/templates_c';//编译后的模板目录
        if(!is_dir($path .'/temp')){
            mkdir($path .'/temp', 0755);
        }
        $this->compile_dir  = $path .'/temp';//编译后的模板目录
        //$this->cache_dir    = $path .'/temp/cache';//smarty 缓存目录
        $this->cache_dir    = $path .'/temp';//smarty 缓存目录
    }
}