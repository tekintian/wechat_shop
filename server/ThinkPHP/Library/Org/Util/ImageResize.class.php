<?php 
/**
* 第三方，乐仁 
* 图片缩放和裁剪类
*/
namespace Org\Util;

class ImageResize
{
	//源图象
	var $_img;
	//图片类型
	var $_imagetype;
	//实际宽度
	var $_width;
	//实际高度
	var $_height;
	

	//载入图片
	public function load($img_name, $img_type=''){
		if(!empty($img_type)) $this->_imagetype = $img_type;
		else $this->_imagetype = $this->get_type($img_name);
		switch ($this->_imagetype){
			case 'gif':
				if (function_exists('imagecreatefromgif'))	$this->_img=imagecreatefromgif($img_name);
				break;
			case 'jpg':
				$this->_img=@imagecreatefromjpeg($img_name);
				break;
			case 'png':
				$this->_img=@imagecreatefrompng($img_name);
				imagesavealpha($this->_img,true);
				break;
			default:
				$this->_img=@imagecreatefromstring($img_name);
				break;
		}
		$this->getxy();
		if(is_resource($this->_img)) return true; 
	}

	//缩放图片
	public function resize($width=0, $height=0)
	{
		if(!is_resource($this->_img)) return false;
		
		if($width>0 and $height>0)
		{
			$height2 = round($width * $this->_height / $this->_width );
			
			if($height < $height2){
				$height = $height2;
			}else{
				$width = round($height * $this->_width / $this->_height);
			}
			
		}elseif($width>0){
			$height = round($width * $this->_height / $this->_width );
		}elseif($height>0){
			$width = round($height / $this->_width * $this->_height);
		}
		
		$tmpimg = imagecreatetruecolor($width,$height);
		
		if($this->_imagetype=='png'){
			imagealphablending($tmpimg,false);//这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;
			imagesavealpha($tmpimg,true);//这里很重要,意思是不要丢了$tmpimg图像的透明色;
		}
		
		if(function_exists('imagecopyresampled')) imagecopyresampled($tmpimg, $this->_img, 0, 0, 0, 0, $width, $height, $this->_width, $this->_height);
		else imagecopyresized($tmpimg, $this->_img, 0, 0, 0, 0, $width, $height, $this->_width, $this->_height);
		$this->destroy();
		$this->_img = $tmpimg;
		$this->getxy();
	}
	
	//裁剪图片
	public function cut($width, $height, $x=0, $y=0){
		if(!is_resource($this->_img)) return false;
		
		if($height==0){
			$height = round($width * $this->_height / $this->_width );
		}elseif($width==0)
		{
			$width = round($height / $this->_width * $this->_height);
		}
		
		if($width > $this->_width){
		   if($x==0) $x =($width-$this->_width)/2;
		}elseif($width != $this->_width){
		   if($x==0) $x =($this->_width-$width)/2;
		}
		
		if($height > $this->_height){
		   if($y==0) $y =($height-$this->_height)/2;
		}elseif($height != $this->_height)
		{
		   if($y==0) $y = ($this->_height-$height)/2;
		}

		$tmpimg = imagecreatetruecolor($width,$height);
		
		if($this->_imagetype=='png'){
			imagealphablending($tmpimg,false);//这里很重要,意思是不合并颜色,直接用$img图像颜色替换,包括透明色;
			imagesavealpha($tmpimg,true);//这里很重要,意思是不要丢了$tmpimg图像的透明色;
		}
		
		imagecopy($tmpimg, $this->_img, 0, 0, $x, $y, $width, $height);
		$this->destroy();
		$this->_img = $tmpimg;
		$this->getxy();
	}
	
	
	//显示图片
	public function display($destroy=1)
	{
		if(!is_resource($this->_img)) return false;
		switch($this->_imagetype){
			case 'jpg':
			case 'jpeg':
				header("Content-type: image/jpeg");
				imagejpeg($this->_img);
				break;
			case 'gif':
				header("Content-type: image/gif");
				imagegif($this->_img);
				break;
			case 'png':
			default:
				header("Content-type: image/png");
				imagepng($this->_img);
				break;
		}
		if($destroy) $this->destroy();
	}

	//保存图片 $destroy=1 是保存后销毁图片变量，false这不销毁，可以继续处理这图片
	public function save($fname, $destroy=false, $type='' , $zl=100)
	{
		if(!is_resource($this->_img)) return false;
		if(empty($type)) $type = $this->_imagetype;
		switch($type){
			case 'jpg':
			case 'jpeg':
				$ret=imagejpeg($this->_img, $fname,$zl);
				break;
			case 'gif':
				$ret=imagegif($this->_img, $fname);
				break;
			case 'png':
			default:
				$ret=imagepng($this->_img, $fname);
				break;
		}
		if($destroy) $this->destroy();
		return $ret;
	}
	
	//旋转图片
	public function rotate($rotate)
	{
	   if(!is_resource($this->_img)) return false;
	   $this->_img=imagerotate($this->_img, $rotate , 0);
	}
	
	//销毁图像
	public function destroy()
	{
		if(is_resource($this->_img)) imagedestroy($this->_img);
	}
	
	//取得图像长宽
	public function getxy()
	{
		if(is_resource($this->_img)){
			$this->_width = imagesx($this->_img);
			$this->_height = imagesy($this->_img);
		}
	}
	

	//获得图片的格式，包括jpg,png,gif
	public function get_type($img_name)//获取图像文件类型
	{
		if (preg_match("/\.(jpg|jpeg|gif|png)$/i", $img_name, $matches)){
			$type = strtolower($matches[1]);
		}else{
			$type = "string";
		}
		return $type;
	}
}
?>