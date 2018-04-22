<?php
namespace Admin\Controller;
use Think\Controller;
class OrderController extends PublicController{

	/*
	*
	* 构造函数，用于导入外部文件和公共方法
	*/
	public function _initialize(){
		$this->order = M('Order');
		$this->order_product = M('Order_product');

		$order_status = array('10'=>'待付款','20'=>'待发货','30'=>'待收货','40'=>'已收货','50'=>'交易完成');
		$this->assign('order_status',$order_status);
	}


	/*
	*
	* 获取、查询所有订单数据
	*/
	public function index(){
		//搜索
		//获取商家id
		if (intval($_SESSION['admininfo']['qx'])!=4) {
			$shop_id = intval(M('adminuser')->where('id='.intval($_SESSION['admininfo']['id']))->getField('shop_id'));
			if ($shop_id==0) {
				$this->error('非法操作.');
			}
		}else{
			$shop_id = intval($_REQUEST['shop_id']);
		}
		
		$pay_type = trim($_REQUEST['pay_type']);//支付类型
		$pay_status = intval($_REQUEST['pay_status']); //订单状态
		$start_time = intval(strtotime($_REQUEST['start_time'])); //订单状态
		$end_time = intval(strtotime($_REQUEST['end_time'])); //订单状态
		//构建搜索条件
		$condition = array();
		$condition['del'] = 0; 
		$where = '1=1 AND del=0';
		//根据支付类型搜索
		if ($pay_type) {
			$condition['type'] = $pay_type;
			$where .=' AND type='.$pay_type;
			//搜索内容输出
			$this->assign('pay_type',$pay_type);
		}
		//根据订单状态搜索
		if ($pay_status) {
			if ($pay_status<10) {
				//小于10的为退款
				$condition['back'] = $pay_status;
				$where .=' AND back='.intval($pay_status);
			}else{
				//大于10的为正常订单
				$condition['status'] = $pay_status;
				$where .=' AND status='.intval($pay_status);
			}
			
			//搜索内容输出
			$this->assign('pay_status',$pay_status);
		}
		//根据下单时间搜索
		if ($start_time) {
			$condition['addtime'] = array('gt',$start_time);
			$where .=' AND addtime>'.$start_time;
			//搜索内容输出
			$this->assign('start_time',date("Y-m-d",$start_time));
		}
		//根据下单时间搜索
		if ($end_time) {
			$condition['addtime'] = array('lt',$end_time);
			$where .=' AND addtime<'.$end_time;
			//搜索内容输出
			$this->assign('end_time',date("Y-m-d",$end_time));
		}
		/*if ($start_time && $end_time) {
			$condition['addtime'] = array('eq','addtime>'.$start_time.' AND addtime<='.$end_time);
		}*/

		//分页
		$count   = $this->order->where($where)->count();// 查询满足要求的总记录数
		$Page    = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)

		//分页跳转的时候保证查询条件
		foreach($condition as $key=>$val) {
			$Page->parameter[$key]  =  urlencode($val);
		}
		if ($start_time && $end_time) {
			$addtime = 'addtime>'.$start_time.' AND addtime<'.$end_time;
			$Page->parameter['addtime']  =  urlencode($addtime);
		}

		//头部描述信息，默认值 “共 %TOTAL_ROW% 条记录”
		$Page->setConfig('header', '<li class="rows">共<b>%TOTAL_ROW%</b>条&nbsp;第<b>%NOW_PAGE%</b>页/共<b>%TOTAL_PAGE%</b>页</li>');
		//上一页描述信息
	    $Page->setConfig('prev', '上一页');
	    //下一页描述信息
	    $Page->setConfig('next', '下一页');
	    //首页描述信息
	    $Page->setConfig('first', '首页');
	    //末页描述信息
	    $Page->setConfig('last', '末页');
	    /*
	    * 分页主题描述信息 
	    * %FIRST%  表示第一页的链接显示  
	    * %UP_PAGE%  表示上一页的链接显示   
	    * %LINK_PAGE%  表示分页的链接显示
	    * %DOWN_PAGE% 	表示下一页的链接显示
	    * %END%   表示最后一页的链接显示
	    */
	    $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');

		$show    = $Page->show();// 分页显示输出
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$order_list = $this->order->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach ($order_list as $k => $v) {
			$order_list[$k]['u_name'] = M('user')->where('id='.intval($v['uid']))->getField('name');
		}
		//echo $where;
		$this->assign('order_list',$order_list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('admin_qx',$_SESSION['admininfo']['qx']);//后台用户权限，目前设置为超级管理员权限
		$this->display(); // 输出模板

	}

	/*
	*
	* 选择商家里面的省市联动
	*/
	public function get_city(){
		$id=(int)$_GET['id'];

		$data=M('china_city')->where('tid='.intval($id))->field('id,name')->select();
		$i=0;
		$array=array();
		foreach ($data as $v) {
		   $array[$i]['id']=$v['id'];
		   $array[$i]['name']=$v['name'];
		   $i+=1;
		}
		echo json_encode($array);
	}


	/*
	*
	* 查看订单详情
	*/
	public function show(){
		//获取传递过来的id
		$order_id = intval($_GET['oid']);
		if(!$order_id) {
			$this->error('系统错误.');
		}

		//根据订单id获取订单数据还有商品信息
		$order_info = $this->order->where('id='.intval($order_id))->find();
		$order_pro = $this->order_product->where('order_id='.intval($order_id))->select();
		if (!$order_info || !$order_pro) {
			$this->error('订单信息错误.');
		}
		foreach ($order_pro as $k => $v) {
			$data=array();
			$data = unserialize($v['pro_guide']);
			if ($data) {
				$order_pro[$k]['g_name'] = $data['gname'];
			}else{
				$order_pro[$k]['g_name'] = '无';
			}
		}

		$post_info = array();
		if (intval($order_info['post'])) {
			$post_info = M('post')->where('id='.intval($order_info['post']))->find();
		}
		
		$this->assign('post_info',$post_info);
		$this->assign('order_info',$order_info);
		$this->assign('order_pro',$order_pro);
		$this->display();
	}


	/*
	*
	* 修改订单状态，添加物流名称、物流单号
	*/
	public function sms_up(){
		$oid = intval($_POST['oid']);
		$o_info = $this->order->where('id='.intval($oid))->find();
		if (!$o_info) {
			$arr = array();
			$arr = array('returns'=>0 , 'message'=>'没有找到相关订单.');
			echo json_encode($arr);
			exit();
		}

		//接收ajax传过来的值
		$order_status = intval($_POST['order_status']);
		$kuaidi_name = $_POST['kuaidi_name'];
		$kuaidi_num = $_POST['kuaidi_num'];
		if ($o_info['kuaidi_name']==$kuaidi_name && $o_info['kuaidi_num']==$kuaidi_num && intval($o_info['status'])==$order_status) {
			$arr = array();
			$arr = array('returns'=>0 , 'message'=>'修改信息未发生变化.');
			echo json_encode($arr);
			exit();
		}

		try{
			if(($kuaidi_name=='' || $kuaidi_num=='') && $order_status==30) throw new Exception('参数不正确');
			/*$msg = '您的订单（编号:%s）,已发货，送货快递:%s，运单号:%s 【%s】';
			$msg = sprintf($msg,$id,$kuaidi_name,$kuaidi_num,$partner_info['name']);*/
			//修改快递信息
			$data = array();
			if ($order_status) {
				$data['status'] = $order_status;
			}
			if ($kuaidi_name) {
				$data['kuaidi_name'] = $kuaidi_name;
			}
			if ($kuaidi_num) {
				$data['kuaidi_num'] = $kuaidi_num;
			}
			$up = $this->order->where('id='.intval($oid))->save($data);
			$json = array();
			if ($up) {
				$json['message']="操作成功.";
				$json['returns']=1;
			}else{
				$json['message']="操作失败.";
				$json['returns']=0;
			}
		}catch(Exception $e){
			   $json = array('returns'=>0 , 'message'=>$e->getMessage());
		}
		echo json_encode($json);
		exit();
	}


	/*
	*
	*  确认退款  修改退款状态
	*/
	public function back(){
	   $id =(int)$_GET['oid'];
	   $back_info = $this->order->where('id='.intval($id))->find();
	   if(!$back_info || intval($back_info['back'])!=1) {
	   		$this->error('订单信息错误.');
	   }

	   $data = array();
	   $data['back']=2;

	   $up_back = $this->order->where('id='.intval($id))->save($data);
	   if ($up_back) {
	   		$this->success('操作成功.');
	   }else{
	   		$this->error('操作失败.');
	   }
	}

	/*
	*
	*  订单删除方法
	*/
	public function del(){
		//以后删除还要加权限登录判断
		$id = intval($_GET['did']);
		$check_info = $this->order->where('id='.intval($id))->find();
		if (!$check_info) {
			$this->error('系统错误，请稍后再试.');
		}

		$up = array();
		$up['del'] = 1;
		$res = $this->order->where('id='.intval($id))->save($up);
		if ($res) {
			$this->success('操作成功.');
		}else{
			$this->error('操作失败.');
		}
	}


	/*
	*
	*  订单统计功能
	*/
	public function order_count(){
		//查询类型 d日视图  m月视图
		$type = $_GET['type'];
		//查询商家id
		$where = '1=1';

		//获取商家id
		if (intval($_SESSION['admininfo']['qx'])!=4) {
			$shop_id = intval(M('adminuser')->where('id='.intval($_SESSION['admininfo']['id']))->getField('shop_id'));
			if ($shop_id==0) {
				$this->error('非法操作.');
			}
		}else{
			$shop_id = intval($_REQUEST['shop_id']);
		}

		if ($shop_id) {
			$where .= ' AND shop_id='.intval($shop_id);
			$shop_name = M('shangchang')->where('id='.intval($shop_id))->getField('name');
			$this->assign('shop_name',$shop_name);
			$this->assign('shop_id',$shop_id);
		}

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

		  //$hyxl=select('id','aaa_pts_order',"1 $where and addtime>$day and addtime<$dayend",'num');
		  $hyxl = $this->order->where($where.' AND addtime>'.$day." AND addtime<".$dayend)->count('id');
		  $data1.=',['.$i.','.$hyxl.']';
		}
		$this->assign('data1',$data1);
		$this->assign('day_String',$day_String);
		//当天日期的时间戳
		$today = strtotime(date('Y-m-d'));
		$this->assign('today',$today);

		//获取最近订单数据
		$order_list = $this->order->where($where)->order('id desc')->limit('0,20')->select();
		foreach ($order_list as $k => $v) {
			$order_list[$k]['shop_name'] = M('shangchang')->where('id='.$v['shop_id'])->getField('name');
		}
		$this->assign('order_list',$order_list);
		$this->assign('type',$type);
		//print_r($where);die();
		$this->display();
	}

}