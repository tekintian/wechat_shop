<?php
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller{
//***************************************
	public $page=null;//分页对象
	public $perPage;//每页显示条数
	//public $Array;
	public $error_message='服务器繁忙！'; //异常错误信息！
//****************************************
	//****************
	//构造函数
	//****************
	public function __construct(){
		//引入父类的构造函数
		parent::__construct();
		//登录判断
		if(CONTROLLER_NAME!="Login"){
			 if(empty($_SESSION['admininfo'])){
				 echo "<script>alert('请先登录再进行操作...');location.href='".U('Login/index')."'</script>";
				 exit;
			 }
		}
	}
	//**************************
	//原作者自己封装的分页功能
	//js写法，直接复制
	//function product_option(page){
	//  window.location.href='?page='+page+'&message='+$("#message").val()
	//}
	//**************************
	public function page_index($count,$row_page,$page){
		$pare= $page>0 ? '<a onclick="product_option('.($page-1).');">上一页</a>' : '<span>上一页</span>';
		$next= $row_page-1>$page ?  '<a onclick="product_option('.($page+1).');">下一页</a>' : '<span>下一页</span>';

		$text='<span style="color:#666">
		        共 '.$count.' 条&nbsp;&nbsp;&nbsp;
				总页数:'.$row_page.'&nbsp;&nbsp;&nbsp;
				当前页:'.($page+1).'&nbsp;&nbsp;
			   </span>
			   '.$pare.$next.'&nbsp;&nbsp; 
			   <select onchange="product_option(this.value)">';
		
		for($i=0; $i<$row_page; $i++){
		  $page==$i ? $select='selected="selected"' : $select='' ;
		  $text.='<option value="'.$i.'" '.$select.'>'.($i+1).'</option>';
		}
		
	   $text.'</select>';
	   return $text;
	}
	//****************
	//地址枚举
	//****************
	public function city_option($id=0,$tid=0,$f=0){
	    if($id==0 && $tid==0 && $f==0){return;}
	    $priv=M('ChinaCity')->field('tid')->where("id=".(int)$id)->find();
	   
		(int)$tid==0 ? $tid=(int)$priv['tid'] : (int)$tid;
		$city=M('ChinaCity')->field('id,name')->where('tid='.(int)$tid)->select();
		foreach ($city as $k => $v) {
			if ($v) {
				(int)$id==$v['id'] ? $select='selected="selected"' : $select='';
		   		$text.='<option value="'.$v['id'].'" '.$select.'>--'.$v['name'].'</option>';
			}
		}
		return $text;
	}

	//****************
	//地址ajax
	//****************
	public function china_city(){
		$id=(int)$_GET['id'];
		$city=M('ChinaCity')->field('id,name')->where('tid='.(int)$id)->select();
		foreach ($city as $k => $v) {
			$city[$k]['name']=urlencode($v['name']);
		}
		//用urldecode+urlencode解决乱码问题
		echo urldecode(json_encode($city));
	}
	//****************
	//中文字符串截取
	//****************
	public function str_substr($title,$length){
		$encoding='utf-8';
		if(mb_strlen($title,$encoding)>$length){
		   $title=mb_substr($title,0,$length,$encoding).'...';
		}
		return $title;
	}
	//********************************
	//方法说明：重写htmlentities参数
	//方法名称：htmlentities_u8
	//*********************************
	public function htmlentities_u8($v){
		return $v=='' ? '' : htmlentities(trim($v),ENT_QUOTES,'utf-8');
	}

	//***********************************
	//方法说明：解析html代码
	//方法名称：html_decode_u8
	//************************************
	public function html_decode_u8($v){
		return $v=='' ? '' : html_entity_decode($v,ENT_QUOTES,'utf-8');
	}
	//***********************************
	//方法说明：tp内置普通分页
	//方法名称：normalPage
	//************************************
	public function normalPage($model){
		$model=M($model);
		$count      = $model->count();// 查询满足要求的总记录数
		$this->page       = new \Think\Page($count,$this->perPage);// 实例化分页类 传入总记录数和每页显示的记录数(25)
		$this->page->lastSuffix=false;
		$this->page->pageClass='number';
		$this->page->pageHoverItem='a';
		$this->page->pageHoverClass='';
		$this->page->setConfig('prev','上一页');
		$this->page->setConfig('next','下一页');
		$this->page->setConfig('first','首页');
		$this->page->setConfig('last','尾页');
		$this->page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
		$this->pageShow       = $this->page->show();// 分页显示输出// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
	}

	//*********************************************************
	//方法说明：推荐分类的递归查询，目前只有2级,理论支持无极
	//*********************************************************
	public function product_option($id=0,$lv=0){
	    //将需要递归查询的大分类ID传进来，然后查询
	    $sql=M('category')->field('id,name,bz_2')->where("tid=".$id." and id!=".$id."")->select();
	    $hot='';
	   	foreach ($sql as $k => $v) {
	   		if($v['bz_2']==1){
	   			if($hot=="" && $lv==0){
	   				$hot.='id='.$v['id'];
	   			}else{
	   				$hot.=' or id='.$v['id'];
	   			}
	   		}
		   if(M('category')->where('tid='.$v['id'])->select()>0){
			   $hot.=$this->product_option($v['id'],$lv+1);
		   }
	   	}
		return $hot;
	}

	/*
	*
	* 图片上传的公共方法
	*  $file 文件数据流 $exts 文件类型 $path 子目录名称
	*/
	public function upload_images($file,$exts,$path){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =  2097152 ;// 设置附件上传大小2M
		$upload->exts      =  $exts;// 设置附件上传类型
		$upload->rootPath  =  './Data/UploadFiles/'; // 设置附件上传根目录
		$upload->savePath  =  ''; // 设置附件上传（子）目录
		$upload->saveName = time().mt_rand(100000,999999); //文件名称创建时间戳+随机数
		$upload->autoSub  = true; //自动使用子目录保存上传文件 默认为true
		$upload->subName  = $path; //子目录创建方式，采用数组或者字符串方式定义
		// 上传文件 
		$info = $upload->uploadOne($file);
		if(!$info) {// 上传错误提示错误信息
		    return $upload->getError();
		}else{// 上传成功 获取上传文件信息
			//return 'UploadFiles/'.$file['savepath'].$file['savename'];
			return $info;
		}
	}	

}