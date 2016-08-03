<?php
/*
$data=array('file'=>'file',
		'path'=>'sss/bb/',
		'name'=>''
		);
	$arr=uploadfile($data);
	if($arr['status']==1)
	{
		echo $arr['file'];	
	}
	else
	{
		echo $arr['error'];	
	}
*/
class upload
{
	var $posturl='http://img.test.cn/upload.php';
	var $maxsize=2;// 上传图片最大2M
	var $exts =array(".gif", ".png", ".jpg", ".jpeg", ".bmp");
	var $field;
	var $path;
	var $name;
	function upload($data)
	{
		$this->field=$data['field'];
		$this->path=$data['path'];
		$this->name=$data['name'];
		$this->exts=$data['exts'];
	}
	private function getext($filename)
	{
		return 	strtolower(strrchr($filename,"."));
	}
	private function check()
	{
		$arr['status']=0;
		if(empty($this->field))
		{
			$arr['error']="无上传控件 ！";
			return $arr;
		}
		if(empty($this->exts))
		{
			if(exif_imagetype($_FILES[$this->field]['tmp_name'])<1)
			{						
				$arr['error']="发现可疑上传文件 ！";
				return $arr;
			}
		}
		else
		{
			$ext=$this->getext($_FILES[$this->field]['name']);
			if(!in_array($ext,$this->exts))
			{
				$arr['error']="发现可疑上传文件 ！";
				return $arr;
			}
		}
		if($_FILES[$this->field]['size']>1048576*2)//2M
		{
			$arr['error']="大小限制：2M！";
			return $arr;
		}
		return true;
	}
	function uploadfile()
	{		
		$check=$this->check();
		if($check !==true){return $check;}
		
		$name	=empty($this->name)?time().rand(100,999):$this->name;
		$ext	=$this->getext($_FILES[$this->field]['name']);		
		$post=array();
		$post['key']		='fenecll_upload_img';
		$post['field']		='@'.$_FILES[$this->field]['tmp_name'];			
		$post['filename']	=$name.$ext;
		$post['filepath']	=$this->path;
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->posturl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		$result = curl_exec($curl);	
		curl_close($curl);
		$result=json_decode($result,true);
		if($result['status']==1)
		{
			$arr['status']=1;
			$arr['file']=$result['file'];
			return $arr;
		}
		else
		{
			$arr['status']=0;
			$arr['error']=$result['error'];
			return $arr;
		}	
	}
	function getfilelist()
	{			
		$post=array();
		$post['key']		='fenecll_get_imglist';		
		$post['filepath']	=$this->path;
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->posturl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
		$result = curl_exec($curl);	
		curl_close($curl);
		return $result;	
	}	
}