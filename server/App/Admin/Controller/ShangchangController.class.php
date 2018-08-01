<?php
namespace Admin\Controller;
use Think\Controller;
class ShangchangController extends PublicController{
	//**********************************************
	//说明：店铺管理 推荐 修改 删除 列表 搜索
	//**********************************************
	public function index(){
		//===================
		// GET获得的数据集合
		//===================
		$id=(int)$_GET['id'];
		$type=$_GET['type'];
		$tuijian=$_GET['tuijian']!=NULL ? (int)$_GET['tuijian'] : '';
		$name=$this->htmlentities_u8($_GET['name']);
		$sheng=$_GET['sheng']!=NULL ? (int)$_GET['sheng'] : '';
		$city=$_GET['city']!=NULL ? (int)$_GET['city'] : '';
		$quyu=$_GET['quyu']!=NULL ? (int)$_GET['quyu'] : '';
		
		//===================================================
		// 查询省市区数据,先将省查出来,后面用ajax+js将市区补上
		//===================================================
		$output_sheng=$this->city_option($sheng,0,1);
		$output_city=$this->city_option($city,$sheng);
		$output_quyu=$this->city_option($quyu,$city);

		//===============================
		// 数据查询和搜索
		//===============================
		$where="status=1";
		$name!='' ? $where.=" and name like '%$name%'" : null;
		$tuijian!=='' ? $where.=" and type=$tuijian" : null;
		//地区搜索
		if($quyu>0){
			$where.=" and quyu=$quyu";
		}elseif($city>0){
			$where.=" and city=$city";
		}elseif($sheng>0){
			$where.=" and sheng=$sheng";
		}
		define('rows',20);
		$count=M('shangchang')->where($where)->count();
		$rows=ceil($count/rows);
		$page=(int)$_GET['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$page_index=$this->page_index($count,$rows,$page);
		$shangchang=M('shangchang')->where($where)->order('addtime desc')->limit($limit,rows)->select();
		//组装数据
		foreach ($shangchang as $k => $v) {
			$sheng=M('ChinaCity')->where('id='.intval($v['sheng']))->find();
			$shangchang[$k]['logo']=$v['logo'];
			$shangchang[$k]['zn-sheng']=$sheng['name'];
		}
		//dump($shangchang);exit;
		//==========================
		// 将GET到的数据再输出
		//==========================
		$this->assign('tuijian',$tuijian);
		$this->assign('name',$name);
		$this->assign('type',$type);
		$this->assign('sheng',$sheng);
		$this->assign('city',$city);
		$this->assign('quyu',$quyu);
		//=============
		// 将变量输出
		//=============	
		$this->assign('output_sheng',$output_sheng);
		$this->assign('output_city',$output_city);
		$this->assign('output_quyu',$output_quyu);
		$this->assign('page_index',$page_index);
		$this->assign('shangchang',$shangchang);
		$this->display();
	}

	//**********************************************
	//说明：店铺管理 添加 修改
	//**********************************************
	public function add(){	
		$id=(int)$_GET['id'];
		//==================================
		// GET获得的数据集合
		//==================================
		$type=$_GET['type'];
		$page=$_GET['page'];
		$tuijian=$_GET['tuijian']!=NULL ? (int)$_GET['tuijian'] : '';
		$name=$this->htmlentities_u8($_GET['name']);

		if($_SESSION['admininfo']['qx']==4){
			$sheng=$_GET['sheng']!=NULL ? (int)$_GET['sheng'] : '';
			$city=$_GET['city']!=NULL ? (int)$_GET['city'] : '';
			$quyu=$_GET['quyu']!=NULL ? (int)$_GET['quyu'] : '';
		}else{
			$scq=M('shangchang')->where('id='.(int)$_SESSION['admininfo']['shop_id'])->find();
			$sheng=$scq['sheng'];
			$city=$scq['city'];
			$quyu=$scq['quyu'];
		}
		//dump($scq);exit;
		//==========================================
		// 组装post过来的数据进行处理添加
		//==========================================
		if($_POST['submit']==true){
		   $id = intval($_POST['id']);
		   if($_POST['location']!=''){
			   $location=explode(',',$_POST['location']);
		   }
		   //组装一个省市区的名字出来
		   $post_sheng=M('ChinaCity')->where('id='.(int)$_POST['sheng'])->getField('name');
		   $post_city =M('ChinaCity')->where('id='.(int)$_POST['city'])->getField('name');
		   $post_quyu =M('ChinaCity')->where('id='.(int)$_POST['quyu'])->getField('name');
		   $array=array(
				'name' => $_POST['name'] ,
				'uname' => $_POST['uname'] ,
				'sheng' => (int)$_POST['sheng'] ,
				'city' => (int)$_POST['city'] ,
				'quyu' => (int)$_POST['quyu'] ,
				'address' => $_POST['address'] ,
				'address_xq' => $post_sheng.' '.$post_city.' '.$post_quyu.' '. $_POST['address'] ,
				'location_x' => $location[0] ,
				'location_y' => $location[1] ,
				'tel' => $_POST['tel'] ,
				'utel' => $_POST['utel'] ,
				'content' => $_POST['content'] ,
				'sort' => $_POST['sort'] ,
				'updatetime' => time() ,
				'status' => $_POST['status'] ? 1 : 0 ,
				'qq' => $_POST['qq'] ,
				'intro' => $_POST['intro'] ,
				'cid'=> intval($_POST['cid'])
		    );
				  

			//logo上传处理
			if (!empty($_FILES["logo"]["tmp_name"])) {
				//文件上传
				$info2 = $this->upload_images($_FILES["logo"],array('jpg','png','jpeg'),"shop/logo/".date(Ymd));
				if(!is_array($info2)) {// 上传错误提示错误信息
					$this->error($info2);
					exit();
				}else{// 上传成功 获取上传文件信息
					$array['logo'] = 'UploadFiles/'.$info2['savepath'].$info2['savename'];
					if (intval($id)) {
						$check_logo = M('shangchang')->where('id='.intval($id))->getField('logo');
						$url = "Data/".$check_logo;
						if (file_exists($url) && $check_logo) {
							@unlink($url);
						}
					}
				}
			}

			//商铺背景图上传处理
			if (!empty($_FILES["vip_char"]["tmp_name"])) {
				//文件上传
				$info2 = $this->upload_images($_FILES["vip_char"],array('jpg','png','jpeg'),"shop/".date(Ymd));
				if(!is_array($info2)) {// 上传错误提示错误信息
					$this->error($info2);
					exit();
				}else{// 上传成功 获取上传文件信息
					$array['vip_char'] = 'UploadFiles/'.$info2['savepath'].$info2['savename'];
					if (intval($id)) {
						$check_bg = M('shangchang')->where('id='.intval($id))->getField('vip_char');
						$url = "Data/".$check_bg;
						if (file_exists($url) && $check_bg) {
							@unlink($url);
						}
					}
				}
			}
		   
		  if($id>0){
			  //将空数据排除掉，防止将原有数据空置
			  foreach ($array as $k => $v) {
			  	 if(empty($v)){
			  	 	unset($v);
			  	 }
			  }
			  $partner =M('shangchang')->where('id='.intval($id))->save($array);
		  }else{
			  $array['addtime']=time();
			  $partner =M('shangchang')->add($array);
			  $id = $partner;
		  }
		  if($partner){			  
			   echo '
			   <script>
				  location= !confirm("操作成功！\n是否继续操作？") ? "?page='.$page.'name='.$name.'&type='.$type.'&sheng='.$sheng.'&city='.$city.'&quyu='.$quyu.'&tiaojian='.$tiaojian.'&level='.$level.'" : "?id='.$id.'&page='.$page.'&name='.$name.'&type='.$type.'&sheng='.$sheng.'&city='.$city.'&quyu='.$quyu.'&tiaojian='.$tiaojian.'&level='.$level.'";
			   </script>';
		   }else{
			   $this->error('保存失败！');
			   exit();
		  	}
		}

		//=============================
		// 查询店铺的所有信息出来
		//=============================
		$shangchang= $id>0 ? M('shangchang')->where('id='.$id)->find() : '';
		//=======================================================
		// 查询省市区数据,先将省查出来,后面用ajax+js将市区补上
		//=======================================================
		$output_sheng=$this->city_option($sheng,0,1);
		$output_city=$this->city_option($city,$sheng);
		$output_quyu=$this->city_option($quyu,$city);
		//=============================
		// 查询店铺的所有分类
		//=============================
		$clist = M('sccat')->where('1=1')->order('addtime desc')->select();
		//==========================
		// 将GET到的数据再输出
		//==========================
		$this->assign('id',$id);
		$this->assign('page',$page);
		$this->assign('type',$type);
		$this->assign('name',$name);
		$this->assign('tuijian',$tuijian);
		$this->assign('sheng',$sheng);
		$this->assign('city',$city);
		$this->assign('quyu',$quyu);
		//=========================
		// 将变量输出
		//=========================	
		$this->assign('output_sheng',$output_sheng);
		$this->assign('output_city',$output_city);
		$this->assign('output_quyu',$output_quyu);
		$this->assign('clist',$clist);
		$this->assign('shangchang',$shangchang);
		$this->display();
		
	}

	//***************************
	//说明：产品 设置推荐
	//***************************
	public function set_tj(){
		$id = intval($_REQUEST['id']);
		$tj_update=M('shangchang')->where('id='.intval($id).' AND status=1')->find();
		if (!$tj_update) {
			$this->error('商家不存在或未通过审核！');
			exit();
		}

		//查推荐type
		$data = array();
		$data['type'] = $tj_update['type']==1 ? 0 : 1;
		$up = M('shangchang')->where('id='.intval($id))->save($data);
		if ($up) {
			$this->redirect('index',array('page'=>intval($_REQUEST['page'])));
			exit();
		}else{
		    $this->error('操作失败！');
			exit();
		}
	}

	//***************************
	//说明：产品 删除
	//***************************
	public function del()
	{
		$id = intval($_REQUEST['did']);
		$info = M('shangchang')->where('id='.intval($id))->find();
		if (!$info) {
			$this->error('商家不存在！'.__LINE__);
			exit();
		}

		$data=array();
		$data['status'] = $info['status'] == '1' ?  0 : 1;
		$up = M('shangchang')->where('id='.intval($id))->save($data);
		if ($up) {
			$this->redirect('index',array('page'=>intval($_REQUEST['page'])));
			exit();
		}else{
			$this->error('操作失败.');
			exit();
		}
	}	

	//**********************************************
	//说明：百度坐标捕获
	//**********************************************
	public function baidumap(){
		$this->display();
	}

	//**********************************************
	//说明：跳转店铺修改页面
	//**********************************************
	public function password(){
		//判断session里面是否有登录商家id
		if (!$_SESSION['admininfo']['id']) {
			$this->error('非法操作.');
			exit;
		}

		//**********************************************
		//说明：接收新密码，修改数据
		//**********************************************
		if ($_POST['submit']==true) {
			$old_pwd = $_POST['old_password'];
			$pwd = $_POST['password'];

			//**********************************************
			//说明：获取会员密码，判断是否为空，是否和新密码匹配
			//**********************************************
			$user_info = M('adminuser')->where('id='.intval($_SESSION['admininfo']['id']).' AND del=0')->find();
			if (!$user_info) {
				$this->error('系统错误，请售后再试.');
				exit;
			}

			if ($user_info['pwd']!==md5(md5($old_pwd))) {
				$this->error('原始密码错误.');
				exit;
			}

			$up=array();
			$up['pwd']=pw_tmp_en($pwd);
			$result = M('adminuser')->where('id='.intval($_SESSION['admininfo']['id']))->save($up);
			if ($result) {
				$this->success('操作成功.');
				die();
			}else{
				$this->error('修改失败。请稍后再试.');
				die();
			}
		}

		$this->display();
	}

	//***********************************
	// 店铺列表的销售统计，
	// 发送过来的是shop_id
	//**********************************
	public function product_tj(){
		$aaa_pts_qx=1;
		if($_SESSION['admininfo']['qx']!=4){
			$shop_id =(int) M('adminuser')->where('id='.$_SESSION['admininfo']['id'])->getField('shop_id');
			if($shop_id==0){
			   echo '必须先绑定店铺';
			   return;	
			}
		}else{
		    $shop_id=(int)$_GET['shop_id'];
		}

		$type = $_GET['type'];
		$where="1=1";
		$where.= $shop_id>0 ? ' and shop_id='.$shop_id : '';
		for($i=0;$i<12;$i++){
		  //日期
		  if($type=='m'){
			 $day = strtotime(date('Y-m')) - 86400*30*(11-$i);
			 $dayend = $day+86400*30;
			 $day_String .= ',"'.date('Y/m',$day).'"';
		  }else{
			 $day = strtotime(date('Y-m-d')) - 86400*(11-$i);
			 $dayend = $day+86400; 
			 $day_String .= ',"'.date('m/d',$day).'"';
		  }
		  //dump($dayend);exit;
		  //$hyxl=select('id','aaa_pts_order',"1 $where and addtime>$day and addtime<$dayend",'num');
		  $hyxl=M('order')->field('id')->where("$where AND addtime>$day AND addtime<$dayend")->count();
		  $data1.=',['.$i.','.$hyxl.']';
		}

		$today = strtotime(date('Y-m-d'));

		//$tsql="select * from aaa_pts_order where 1 $where";
		define('rows',20);
		$count=M('order')->where($where)->count();
		$rows=ceil($count/rows);
		$orderlist= M('order')->where($where)->order('id desc')->limit(0,rows)->select();
		foreach ($orderlist as $k => $v) {
			$orderlist[$k]['shangchang']=M('shangchang')->where('id='.$v['shop_id'])->getField('name');
			$orderlist[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
		}
		//根据get过来的shop_id输出商家名字
		$shop_id>0 ? $shangchang=M('shangchang')->where("id=$shop_id")->getField('name') : NULL;
		//=========================
		// 将变量输出
		//=========================	
		$this->assign('id',$id);
		$this->assign('shop_id',$shop_id);
		$this->assign('day_String',$day_String);
		$this->assign('data1',$data1);
		$this->assign('type',$type);
		$this->assign('orderlist',$orderlist);
		$this->assign('shangchang',$shangchang);
		$this->display();
	}
}