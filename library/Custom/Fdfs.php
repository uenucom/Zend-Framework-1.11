<?php
class Custom_Fdfs{

	public $tracker;
	public $server;
	public $storage;
	public  function __construct(){
		$this->tracker = fastdfs_tracker_get_connection();
		$this->server = fastdfs_connect_server($this->tracker['ip_addr'], $this->tracker['port']);
		$this->storage = fastdfs_tracker_query_storage_store();

		$this->server = fastdfs_connect_server($this->storage['ip_addr'], $this->storage['port']);
		if(!$this->server){
			error_log("errno1: ". fastdfs_get_last_error_no() . ", error info: ". fastdfs_get_last_error_info());
			exit(1);
		}

		$this->storage['sock'] = $this->server['sock'];

	}

	public  function fdfsUpload($input_name,$size=0){

		if(($size!=0)&&($_FILES[$input_name]['size']>$size)){
			$arr = array('statusCode'=>300,'message'=>"上传文件过大");
			$result = $this->returnParams($arr);
			$result = json_encode($result);
			echo $result;
			exit();
		}
		$file_tmp= $_FILES[$input_name]['tmp_name'];
		$real_name= $_FILES[$input_name]['name'];
		$file_name= dirname($file_tmp)."/".$real_name;
		//copy($file_tmp, $file_name);
		rename($file_tmp, $file_name);

		$file_info=  fastdfs_storage_upload_by_filename($file_name, null, array(), null, $this->tracker, $this->storage);
		//print_r($file_info);die;
		if($file_info){
			$group_name= $file_info['group_name'];
			$remote_filename= $file_info['filename'];

			$i= fastdfs_get_file_info($group_name, $remote_filename);
			$storage_ip= $i['source_ip_addr'];
			//var_dump($file_info);
			return array($remote_filename, $group_name, $storage_ip, $real_name);
		}
		return false;
	}

	public function fdfsDown($group_name, $file_id){
		$file_content= fastdfs_storage_download_file_to_buff($group_name, $file_id);
		return $file_content;
	}

	public  function fdfsDel($group_name, $file_id){
		fastdfs_storage_delete_file($group_name, $file_id);
	}

	public  function fdfsUploaddir($file_name,$size=0){
		$file_info=  fastdfs_storage_upload_by_filename($file_name, null, array(), null, $this->tracker, $this->storage);
		if(file_exists($file_name)){
				
		} else {
			$arr = array('statusCode'=>300,'message'=>substr($file_name, 55)."文件不存在，或者命名错误,这条之前的信息添加成功");
			$result = $this->returnParams($arr);
			$result = json_encode($result);
			echo $result;
			exit();
		}
		$filesize=abs(filesize($file_name));
		if(($size!=0)&&($filesize>$size)){
			$arr = array('statusCode'=>300,'message'=>$file_name."上传文件过大,这条之前的信息添加成功");
			$result = $this->returnParams($arr);
			$result = json_encode($result);
			echo $result;
			exit();
		}
		//print_r($file_info);die;
		if($file_info){
			$group_name= $file_info['group_name'];
			$remote_filename= $file_info['filename'];

			$i= fastdfs_get_file_info($group_name, $remote_filename);
			$storage_ip= $i['source_ip_addr'];
			//var_dump($file_info);
			return array($remote_filename, $group_name, $storage_ip, $file_name);
		}
		return false;
	}


	//统一返回参数
	private  function returnParams($arr=array()){
		$result = array('statusCode'=>200,'message'=>'ok','navTabId'=>'','rel'=>'','callbackType'=>'','forwardUrl'=>'','confirmMsg'=>'');
		foreach ($arr as $key => $value) {
			if(array_key_exists($key, $result)){
				$result[$key] = $value;
			}
		}
		return  $result;
	}
}
