<?php
/*
 * 图片上传，支持缩略图
 * author：liguibing
 * date： 2013-04-11
 */
error_reporting(0);
define('UPLOAD_FILE_EMPTY',            101);  //上传的附件、文件为空
define('UPLOAD_FILE_ILLEGAL',          102);  //上传文件非法
define('UPLOAD_FILE_POSTFIX_ILLEGAL',  103);  //上传文件格式非法
define('UPLOAD_FILE_SIZE_ILLEGAL',     104);  //上传文件大小非法
define('UPLOAD_FILE_FAILURE',          105);  //上传文件失败
define('UPLOAD_FILE_SIZE_TOOSMALL',    106);  //图片像素太小
define('UPLOAD_FILE_LAYER',            107);  //分割文件名宽度超出范围
define('UPLOAD_FILE_CREATE_DIR',       108);  //创建目录没有权限

class upload_file{
    //默认配置
    public $_config = array (
            'path'      => 'upload',                           //上传物理地址前缀
            'fileType'  => 'image',                            //类型为图片
            'cutting'   => FALSE,                              //是否需要切图 
            'cutWidth'  => 0,                                  //默认等比例缩微切割宽度
            'cutHeight' => 0,                                  //默认等比例缩微切割高度
            'allowType' => array(1 => 'gif', 2 => 'jpeg', 
                                 3 => 'png', 6 => 'bmp','jpg'),    //可以上传的图片类型
            'checkType' => TRUE,                               //是否检查图片格式
            'checkSize' => TRUE,                               //是否检查图片大小
            'maxSize'   => 5120000,                            //上传最大值, 单位字节
            'autoDir'   => TRUE,                               //默认开启目录自动分割, 只上传原图有效
            'cutLayer'  => 2                                   //目录分割, 文件名宽度
    );
    
    //getimagesize + $_FILES值
    public  $getData = '';
    private $dir_sep = DIRECTORY_SEPARATOR;
    
    //文件上传配置初始化
    public function __construct ($config = array()) {
        if ($config && is_array($config)) {
            foreach ($this->_config as $k => $v) {
                if (isset($config[$k])) {
                    $this->_config[$k] = $config[$k];
                }
            }
        }

        unset($config);
    }
    
    //文件原图上传
    public function put($arr_files = array()) {
        $this->getData = & $arr_files;

        if(!isset($this->getData['tmp_name']) || !isset($this->getData['name']) || empty($this->getData['tmp_name']) || empty($this->getData['name'])) {
            $this->halt(UPLOAD_FILE_EMPTY);
        }
        
        if(isset($this->getData['error']) && $this->getData['error']) {
            $this->halt(UPLOAD_FILE_FAILURE);
        }
        
        if(isset($this->getData['cutLayer']) && ($this->getData['cutLayer'] > 32 || $this->getData['cutLayer'] < 2)) {
            $this->halt(UPLOAD_FILE_FAILURE);
        }

        if(is_uploaded_file($this->getData['tmp_name'])) {
            $this->checkSafe();
            $this->createNewPath($this->_config['autoDir'], $this->_config['cutLayer']);
            $this->createDir();

            //图片或者文件都可以上传
            return $this->doUpload() ? $this->getData : UPLOAD_FILE_FAILURE;
        }
    }

    /**
     * 文件上传开始
     *
     * @param  <string> tmp_name
     * @param  <string> path, new_path
     * @return <boolean>
     */
    private function doUpload() {
        if(move_uploaded_file($this->getData['tmp_name'], $this->getData['full_path'])) {
            return TRUE;
        }

        return FALSE;
    }
    
    /**
     * 判断上传文件大小是否合法
     *
     * @param  <number> size文件大小值
     * @return <boolean>
     */
    private function checkSize() {
        if($this->_config['checkSize'] && $this->getData['size'] > $this->_config['maxSize']) {
            return FALSE;
        }

        return TRUE;
    }
    
    /**
     * 取得文件名后缀
     *
     * @param  <string>  name
     * @return <string> 返回后缀名称
     */
    private function getPostfix() {
        return strtolower(trim(substr(strrchr($this->getData['name'], '.'), 1, 10)));
    }
    
    /**
     * 检查文件是否为规定上传类型
     *
     * @param  <string> name
     * @return <boolean>
     */
    private function checkFormat() {
        if($this->_config['checkType'] && !in_array($this->getPostfix(), $this->_config['allowType'])) {
            return FALSE;
        }

        return TRUE;
    } 
    
    /**
     * 创建分隔目录
     *
     * @param  <string> path配置目录, new_path完整目录
     */
    private function createDir() {
        if(!file_exists($path = dirname($this->getData['full_path']))) {
            if(!@mkdir($path, 0777, TRUE)) {
                return UPLOAD_FILE_CREATE_DIR;
            }
        }
    }
    
    /**
     * $autoDir 如果为假, 目录分割功能关闭
     *
     * @param  <string> new_name, new_dir, new_path
     * @return <string> new_dir, new_path
     */
    private function createNewPath($autoDir = TRUE, $cutLayer = 2) {
        $time = time();
        $this->getData['new_name'] = md5($time . rand(1, 100)). '.' . $this->getPostfix();
        $this->getData['new_dir'] =  $this->dir_sep;

        if($autoDir) {
            $this->getData['new_dir'] =  date('Ym', $time). $this->dir_sep;
            $this->getData['new_dir'] .= substr($this->getData['new_name'], 0, $cutLayer). $this->dir_sep;
        }
        
        $this->getData['full_path'] = $this->_config['path']. $this->dir_sep. $this->getData['new_dir'] . $this->getData['new_name'];
    }

    /**
     * 图片有效性检测
     *
     * @param   <string> fullPath 完整全路径地址
     * @return  <string> width图片宽度, height图片高度, img_type_number图片数字类型
     */
    private function checkSafe() {
        if(!$this->checkFormat()) {
            $this->halt(UPLOAD_FILE_POSTFIX_ILLEGAL);
        }

        if(!$this->checkSize()) {
            $this->halt(UPLOAD_FILE_SIZE_ILLEGAL);
        }

        $this->getImage();
    }
    
    /**
     * 取得图片的长、宽、类型
     *
     * @param   <string> fullPath 完整全路径地址
     */
    private function getImage($fullPath = '') {
        if ($this->_config['fileType'] == 'image') {
            list($this->getData['width'], $this->getData['height'], $this->getData['img_type_number']) = getimagesize($fullPath ? $fullPath : $this->getData['tmp_name']);
            if(!in_array($this->getData['img_type_number'], @array_keys($this->_config['allowType']))) {
                $this->halt(UPLOAD_FILE_POSTFIX_ILLEGAL);
            }
        }
    }
        
    /**
     * 生成等比例缩微图thumb
     *
     * @param  <string> $oldFileName   原始文件名 08b44de34eb6f5460f374f8bd418b9bd.gif
     * @param  <string> $newFileName   新文件名   08b44de34eb6f5460f374f8bd418b9bd_thumb.gif
     * @param  <string> $dirPath       把生成的新图片存放到另一个目录中
     *
     * 注： 多切只需要传文件名即可, 不带后缀
     * @param newThumbName 新文件名  thumb
     * @return <boolean>   TRUE 成功, FALSE 失败
     */
    public function putThumb($oldFileName = '', $newFileName = 'thumb', $dirPath = '') {
        $filePath = $oldFileName ? $this->_config['path']. $this->dir_sep. $oldFileName : $this->getData['full_path'];

        if($filePath && $this->_config['fileType'] == 'image' && $this->_config['cutting'] && $this->_config['cutWidth'] && $this->_config['cutHeight'] && @file_exists($filePath)) {

            if(!isset($this->getData['width']) || !isset($this->getData['height'])) {
                $this->getImage($filePath);
            }
            
            if(!in_array($this->getData['img_type_number'], @array_keys($this->_config['allowType']))) {
                return UPLOAD_FILE_POSTFIX_ILLEGAL;
            }

            switch($this->getData['img_type_number']) {
                case 1:
                    $im = imagecreatefromgif($filePath);
                break;
                case 2:
                    $im = imagecreatefromjpeg($filePath);
                break;
                case 3:
                    $im = imagecreatefrompng($filePath);
                break;
            }

            if(!$im) {
                return FALSE;
            }

            $this->_config['cutWidth'] > $this->getData['width'] && $this->_config['cutWidth'] =  $this->getData['width'];
            $this->_config['cutHeight'] > $this->getData['height'] && $this->_config['cutHeight'] = $this->getData['height'];
            
            $width = $this->_config['cutWidth']; $height = $this->_config['cutHeight'];
            if ($this->_config['cutWidth'] && ($this->getData['width'] < $this->getData['height'])) {
                $width = ($this->_config['cutHeight'] / $this->getData['height']) * $this->getData['width'];
            } else {
                $height = ($this->_config['cutWidth'] / $this->getData['width']) * $this->getData['height'];
            }

            if (function_exists("imagecreatetruecolor")) {
                $new = imagecreatetruecolor($width, $height);
                $this->getData['img_type_number'] == 3 && $this->transparent($new);
                imagecopyresampled($new, $im, 0, 0, 0, 0, $width, $height, $this->getData['width'], $this->getData['height']);
            } else {
                $new = imagecreate($width, $height);
                $this->getData['img_type_number'] == 3 && $this->transparent($new);
                imagecopyresized($new, $im, 0, 0, 0, 0, $width, $height, $this->getData['width'], $this->getData['height']);
            }

            $newFilePath = $dirPath ? $dirPath. $this->dir_sep. $newFileName : trim(substr($this->getData['full_path'], 0, @strrpos($this->getData['full_path'], '.'))) . '_' . $newFileName;

            switch ($this->getData['img_type_number']) {
                case 1:
                    imagegif($new, $newFilePath. '.gif');
                break;
                case 2:
                    imagejpeg($new, $newFilePath. '.jpeg', 100);
                break;
                case 3:
                    imagepng($new, $newFilePath. '.png');
                break;
            }

            imagedestroy($new); imagedestroy($im);
            unset($oldFileName, $newFileName, $filePath, $newFilePath, $width, $height);
            return TRUE;
        }

        return FALSE;
    }

    /**
     * PNG透明背景图片处理
     *
     * @param  <resource> new 资源
     * @param  <int> transparent 如果分配失败则返回 FALSE
     */ 
    private function transparent($new) {
        $transparent = imagecolorallocatealpha($new , 0 , 0 , 0 , 127);
        imagealphablending($new , false);
        imagefill($new , 0 , 0 , $transparent);
        imagesavealpha($new , true);
    }

    //格式化错误输出
    private function halt($message) {
        return  $message;
    }
}
