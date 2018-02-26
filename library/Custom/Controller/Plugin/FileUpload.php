<?php

require_once 'Zend/Controller/Plugin/Abstract.php';

class Custom_Controller_Plugin_FileUpload extends Zend_Controller_Plugin_Abstract {

    private $uploaddir; //文件上传存路径
    private $max_files; //一次性最多上传多少文件
    private $max_size; //文件最大量
    private $permission; //文件夹是否可以有权限
    private $files;
    private $allowed = array(); //允许上传文件格式
    private $notallowed = array("exe", "mp3"); //不允许上传文件格式
    private $filesname; //文件表单name
    private $imagewidth; //图片文件宽度，超过就生成缩略图
    private $imageheight; //图片文件高度，超过就生成缩略图
    public $filearray = array(); //返回多个文件名
    public $lastFileName; //返加一个文件名
    public $Error;

    /**
     * Enter description here...
     *
     * @param unknown_type $uploaddir  文件存放路径
     * @param unknown_type $filesname　表单名称
     * @param unknown_type $max_files　最多同时上传几个文件
     * @param unknown_type $max_size　　允许上传文件大小
     * @param unknown_type $allowed　　　允许上传文件类型
     * @param unknown_type $imagewidth　　图片宽度
     * @param unknown_type $imageheight　　图片高度
     * @return unknown
     */
    public function __construct($uploaddir, $filesname, $max_files = '10', $max_size = '2048000', $allowed = array(), $imagewidth = '500', $imageheight = '500') {
        $this->uploaddir = $uploaddir;
        $this->max_files = $max_files;
        $this->max_size = $max_size;
        $this->permission = 0777;
        $this->allowed = $allowed;
        $this->filesname = $filesname;
        $this->files = &$_FILES;
        $filedir = $this->uploaddir;
        $this->imagewidth = $imagewidth;
        $this->imageheight = $imageheight;

        $remdir = $this->Uploadlocdir($filedir);
        if (!is_writable($remdir)) {
            $this->createFolder($remdir);
        }

        $num = count(array_filter($this->files [$this->filesname] ['name']));

        for ($i = 0; $i < $num; $i ++) {
            if ($this->files [$this->filesname] ['size'] [$i] > $this->max_size) {
                $this->Error = "文件" . $this->files [$this->filesname] ['name'] [$i] . "大小超过" . ($this->max_size / 1000) . "KB";
                return false;
            }
            $file_type = split('[/.-]', $this->files [$this->filesname] ['type'] [$i]);
            $file = strtolower($this->fileExtName($this->files [$this->filesname] ['name'] [$i]));

            if (!in_array($file, $this->allowed)) {
                $this->Error = "文件" . $this->files [$this->filesname] ['name'] [$i] . "类型不允许上传!";
                return false;
            }
        }

        if ($num > $this->max_files) {
            $this->Error = "你上传的文件过多，一次性只能上传" . $this->max_files . "文件";
            return false;
        } else {
            $num = count(array_filter($this->files [$this->filesname] ['name']));
            for ($i = 0; $i < $num; $i ++) {
                $filesize = $this->files [$this->filesname] ['size'] [$i];
                $filename = $this->files [$this->filesname] ['name'] [$i];
                $fileroot = explode(".", $this->files [$this->filesname] ['name'] [$i]);
                // $filename = $filename.'.' . $fileroot [1];

                $this->lastFileName = $filename;
                $this->lastFileType = $fileroot[1];
                $this->lastFileSize = $filesize;

                $this->filearray [$i] = $filename;
                if ($fileroot [1] == 'jpg' || $fileroot [1] == 'jpeg' || $fileroot [1] == 'png' || $fileroot [1] == 'gif' || $fileroot [1] == 'bmp') {
                    $imageborder = $this->files [$this->filesname] ['tmp_name'] [$i];
                    $size = GetImageSize($imageborder);

                    /*
                      if($size [0] > $this->imagewidth || $size [1] > $this->imageheight) {
                      ULoader::libCommonLoader('file','UPath');
                      ULoader::libCommonLoader("file","UImage");
                      $imagecut=new UImage();
                      $image_url=$imagecut->cutPhoto($imageborder,'500',UPath::getPathFromModule('b2b','cut'));
                      $image_url=UPath::toDbPath('b2b','cut',$image_url);
                      } */
                }

                if (!empty($filename)) {
                    $add = $remdir . $filename;
                    if (is_uploaded_file($this->files [$this->filesname] ['tmp_name'] [$i])) {
                        @(int) move_uploaded_file($this->files [$this->filesname] ['tmp_name'] [$i], $add);
                        if (!chmod("$add", $this->permission)) {
                            $this->Error = "你没有权限上传的到此文件夹！";
                            return false;
                        }
                    }
                }
            }
        }
    }

    public function Uploadlocdir($content) {
        $locaddress = $content;
        $last_slash = (substr($content, strlen($content) - 1, 1) == "/");
        if (!$last_slash) {
            $locaddress = ($content . "/");
        }
        return $locaddress;
    }

    public function fileExtName($filename) {
        $filearea = explode(".", $filename); //用.分离文件名
        $partnum = count($filearea); //计算数组中的数目
        $fileclass = $filearea [$partnum - 1]; //得出文件的后缀
        return $fileclass;
    }

    public function createFolder($path) {
        $isTrue = false;
        if (!file_exists($path)) {
            $this->createFolder(dirname($path));
            $isTrue = mkdir($path, 0777);
        }
        return $isTrue;
    }

    public function __destruct() {
        unset($this->uploaddir);
        unset($this->max_files);
        unset($this->max_size);
        unset($this->permission);
        unset($this->allowed);
        unset($this->notallowed);
        unset($this->filesname);
        unset($this->files);
        unset($this->years);
        unset($this->imagewidth);
        unset($this->imageheight);
    }

}
