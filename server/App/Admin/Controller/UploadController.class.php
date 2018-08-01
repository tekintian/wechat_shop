<?php
namespace Admin\Controller;
use Think\Controller;
class UploadController extends PublicController{
	//************************************
	// 说明：图片上传操作方法
	// 包含：活动/商品图片 商标logo 推荐产品图
	//       商家广告图 新闻等
	//  需要提交过来的参数 $_FILES $_POST['type']
	//**************************************
	public function images_action(){
		$aaa_pts_qx=1;
		if($_POST==array()) return;
		//$ImageResize=new \Org\Util\ImageResize;

		$file=$_FILES['file'];
		$type=$_POST['type'];

		//文件格式
		$suffix = strtolower(substr($file['name'],-3));
		$valid_suffix = array('png','jpg');

		//上传图片类型
		switch($type)
		{
		   
		   case 'img1':  //活动/商品图片
		      $array=array(
			          array(600),
			          array(400,175),
					  array(120,120),
			         );
		   break;
		   case 'logo':  //商标logo
		      $array=array(
					  array(230,230),
			         );
		   break;
		   case 'photo_pic':  //推荐产品图
		      $array=array(
					  array(600,340),
			         );
		   break;
		   case 'vip_char':  //商家广告图
		      $array=array(
					  array(400,150),
			         );
		   break;
		   case 'news':  //新闻
		      $array=array(
					  array(120,96),
			         );
		   break;
		   default:
		      $array=array(array((int)$_GET['width'],(int)$_GET['height']));
		   break;	
		}
		try
		{
			if($file==''){
			   throw new \Exception('上传文件不能为空！');
			}
			
			if(!$array){
			   throw new \Exception('没有定义图片尺寸！');
			}
			if(!in_array($suffix,$valid_suffix)){
				 throw new \Exception('不支持该格式的文件上传！');
				 }
				 
			$time=time();
		    $file_url='Data/cache/'.$time.rand(00,99).'.'.$suffix;
			//dump($file_url);exit;
			$v=@move_uploaded_file($file['tmp_name'],$file_url);
			//dump($v);exit;
			if(!$v){
			   throw new \Exception('文件移动失败！');
			}

			 
			 $img=new \Org\Util\ImageResize;
			 
			 // $img_patsh = '../../'.__UPLOAD__.'/';
			 $dir= __UPLOADAPP__.'UploadFiles/%s/'.date("Ym").'/'.date("d");
			 //dump($dir);exit;
			 for($i=0; $i<count($array) ; $i++){
				 $isdir=sprintf($dir,$i);
				 if(!is_dir($isdir)) mkdir($isdir,0700,1);
			 }
		   
			 $ok=$img->load($file_url);
			 if(!$ok){
				 unlink($file_url);
				 throw new \Exception('错误的图片格式！');
				}
			
			 $photo_string='';
			 $i=0;
			 foreach($array as $key=>$val){
				if($type=='img1' && $key==1){
					 $img->load($file_url);
				}
				 $photo = sprintf($dir,$i).'/'.$time.'.'.$suffix;
				 $img->resize($val[0],$val[1]);
				
				 $img->save($photo);
				 $photo_string .= $photo.',';
				 if($type=='img1' and $key==1){
					 $img->load($file_url);
					}
				 $i+=1;
			 }
			 $photo_string = str_replace(__UPLOADAPP__,'',trim($photo_string,','));
			 
			 //释放图片变量
			 $img->destroy();
			 
			 //删除缓存
			 @unlink($file_url);
			 
			 $json=array(
			        'returns'=>true,
					'message'=>$photo_string,
			        );
			
		}
		catch(\Exception $e)
		{
		    $json['returns']=false;
			$json['message']=urlencode($e->getMessage());
		}

		echo '
		    <script>
			   window.parent.html_return('.urldecode(json_encode($json)).');
			</script>
		      ';
	}
	//****************************
	//iframe式添加图片，高端哈？
	//*****************************
	public function images_add(){
		//将iframe过来的src后带参数进行解析输出。
		//$aaa_pts_qx=1;
		$width=$_GET['width']-2;
		$height=$_GET['height']-2;
		$id=$_GET['id'];
		$img=$_GET['img'];
		$images_string=$_GET['images_string'];
		$type=$_GET['type'];
		//==============================
		// 输出变量
		//==============================
		$this->assign('width',$width);
		$this->assign('height',$height);
		$this->assign('id',$id);
		$this->assign('img',$img);
		$this->assign('images_string',$images_string);
		$this->assign('type',$type);
		$this->display();	
	}
	public function img(){
		
		$this->display();	
	}
	public function photo_add(){
		if($_POST['submit']==true){
		  $type=$_GET['type'] == '' ? 'img' : $_GET['type'];
		  $file=$_FILES['file'];
		  $id=$_GET['id'];
		  $width=$_GET['width'];
		  $height=$_GET['height'];
		  
		  $suffix = strtolower(substr($file['name'],-3));
		  $valid_suffix = $_GET['type']=='' ? array('png','gif','jpg') : array('.'.$_GET['type']);
		  //$dir= __UPLOADAPP__.'UploadFiles/%s/'.date("Ym").'/'.date("d");
		  $dir=$_POST['dir']=='' ? __UPLOADAPP__.'UploadFiles' : __UPLOADAPP__.$_POST['dir'];
		  
		  try
		  {
			 
			 if(!in_array($suffix,$valid_suffix) || $id==''){
				 throw new \Exception('不支持该格式的文件上传！');
				 }
				 
		     if($file['size']>2048000){
				  throw new \Exception("上传文件大小不能超过2m");
			  }

		      //上传目录
			  define('dir',$dir.'/'.date("Ymd"));
			  $v=is_dir(dir) ? 1 : @mkdir(dir,0777);
			  if(!$v){
				  throw new \Exception('目录创建失败！');
			  }
			  
			  //上传文件名称
			  $name=date('His').'.'.$suffix;
			  $url=dir.'/'.$name;
			  
			  //移动文件
			  $v=@move_uploaded_file($file['tmp_name'],$url);
			  if(!$v){
				  throw new \Exception($file['tmp_name']);
			  }else{
			  	  $img=new \Org\Util\ImageResize;
				  //$img=new ImageResize();
				  $ok=$img->load($url);
				  if(!$ok){
					 throw new \Exception('错误的图片格式！');
					}
				  if($width!='' && $width!=$img->_width){
					// throw new Exception('图片的尺寸不符合要求，请按照长:'.$width.'、宽:'.$height.'上传！');
					  }
				  if($height!='' && $height!=$img->_height){
					// throw new Exception('图片的尺寸不符合要求，请按照长:'.$width.'、宽:'.$height.'上传！');
					  }
				  $img->destroy();
				  
				  echo '<script>
						   parent.document.getElementById("'.$id.'").value="'.str_replace(__UPLOADAPP__,'',$url).'"
						</script>
						
						<style>*{margin:0;padding:0;}</style>
						<font style="font-size:12px; color:#666;">上传成功</font> 
						<a href="javascript:history.go(-1)" style="font-size:12px; color:#666; text-decoration:none;">重新上传</a>
						'; 
			   }
			  
		  }
		  catch(\Exception $e)
		  {
			  echo '<script>
			           alert("'.$e->getMessage().'");
					   history.go(-1);
			        </script>';
		  }
		 return;
		}
		$this->display();
	}
	
	public function xheditor(){
		$inputName='filedata';//表单文件域name
		$attachDir='./Data/UploadFiles/Uploads';//上传文件保存路径，结尾不要带/
		$dirType=1;//1:按天存入目录 2:按月存入目录 3:按扩展名存目录  建议使用按天存
		$maxAttachSize=1097152;//最大上传大小，默认是2M
		$upExt='txt,rar,zip,jpg,jpeg,gif,png,swf,wmv,avi,wma,mp3,mid';//上传扩展名
		$msgType=2;//返回上传参数的格式：1，只返回url，2，返回参数数组
		$immediate=isset($_GET['immediate'])?$_GET['immediate']:0;//立即上传模式，仅为演示用

		$err = "";
		$msg = "''";
		$tempPath=$attachDir.'/'.date("YmdHis").mt_rand(10000,99999).'.tmp';
		$localName='';

		if(isset($_SERVER['HTTP_CONTENT_DISPOSITION'])&&preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){//HTML5上传
			file_put_contents($tempPath,file_get_contents("php://input"));
			$localName=urldecode($info[2]);
		}
		else{//标准表单式上传
			$upfile=@$_FILES[$inputName];
			if(!isset($upfile))$err='文件域的name错误';
			elseif(!empty($upfile['error'])){
				switch($upfile['error'])
				{
					case '1':
						$err = '文件大小超过了php.ini定义的upload_max_filesize值';
						break;
					case '2':
						$err = '文件大小超过了HTML定义的MAX_FILE_SIZE值';
						break;
					case '3':
						$err = '文件上传不完全';
						break;
					case '4':
						$err = '无文件上传';
						break;
					case '6':
						$err = '缺少临时文件夹';
						break;
					case '7':
						$err = '写文件失败';
						break;
					case '8':
						$err = '上传被其它扩展中断';
						break;
					case '999':
					default:
						$err = '无有效错误代码';
				}
			}
			elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none')$err = '无文件上传';
			else{
				move_uploaded_file($upfile['tmp_name'],$tempPath);
				$localName=$upfile['name'];
			}
		}

		if($err==''){
			$fileInfo=pathinfo($localName);
			$extension=$fileInfo['extension'];
			if(preg_match('/^('.str_replace(',','|',$upExt).')$/i',$extension))
			{
				$bytes=filesize($tempPath);
				if($bytes > $maxAttachSize)$err='请不要上传大小超过'.$this->formatBytes($maxAttachSize).'的文件';
				else
				{
					switch($dirType)
					{
						case 1: $attachSubDir = 'day_'.date('ymd'); break;
						case 2: $attachSubDir = 'month_'.date('ym'); break;
						case 3: $attachSubDir = 'ext_'.$extension; break;
					}
					$attachDir = $attachDir.'/'.$attachSubDir;
					if(!is_dir($attachDir))
					{
						@mkdir($attachDir, 0777);
						@fclose(fopen($attachDir.'/index.htm', 'w'));
					}
					PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
					$newFilename=date("YmdHis").mt_rand(1000,9999).'.'.$extension;
					$targetPath = $attachDir.'/'.$newFilename;
					
					rename($tempPath,$targetPath);
					@chmod($targetPath,0755);
					$targetPath=$this->jsonString(str_replace('./Data','/Data',__ROOT__.$targetPath));
					if($immediate=='1')$targetPath='!'.$targetPath;
					if($msgType==1)$msg="'$targetPath'";
					else $msg="{'url':'".$targetPath."','localname':'".$this->jsonString($localName)."','id':'1'}";//id参数固定不变，仅供演示，实际项目中可以是数据库ID
				}
			}
			else $err='上传文件扩展名必需为：'.$upExt;

			@unlink($tempPath);
		}

		echo "{'err':'".$this->jsonString($err)."','msg':".$msg."}";
	}
	public function jsonString($str)
	{
		return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
	}
	public function formatBytes($bytes) {
		if($bytes >= 1073741824) {
			$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
		} elseif($bytes >= 1048576) {
			$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
		} elseif($bytes >= 1024) {
			$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
		} else {
			$bytes = $bytes . 'Bytes';
		}
		return $bytes;
	}	
}