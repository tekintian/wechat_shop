<?php
namespace Admin\Controller;
use Think\Controller;
class VoucherController extends PublicController{

	//***************************
	//说明：抢购管理页面
	//***************************
	public function index() {
		$keyword = trim($_REQUEST['keyword']);
		$where = '1=1 AND del=0';
		if ($keyword) {
			$where .=' AND title LIKE "%'.$keyword.'%"';
		}

		if (intval($_SESSION['admininfo']['qx'])!=4) {
			$shop_id = intval($_SESSION['admininfo']['shop_id']);
			if (!$shop_id) {
				echo "<script>alert('店铺状态异常！');</script>";
				exit();
			}
			$where .=' AND shop_id='.$shop_id;
		}

		define('rows',10);
		$count=M('voucher')->where($where)->count();
		$rows=ceil($count/rows);
		$page=(int)$_REQUEST['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$voucher_list=M('voucher')->where($where)->order('id desc')->limit($limit,rows)->select();
		$page_index=$this->page_index($count,$rows,$page);
		foreach ($voucher_list as $k => $v) {
			$voucher_list[$k]['shop_name'] = M('shangchang')->where('id='.intval($v['shop_id']))->getField('name');
			$voucher_list[$k]['start_time'] = date("Y-m-d",$v['start_time']);
			$voucher_list[$k]['end_time'] = date("Y-m-d",$v['end_time']);
		}

		$this->assign('keyword',$keyword);
		$this->assign('voucher_list',$voucher_list);
		$this->assign('page_index',$page_index);
		$this->display();
	}

	//********************************
	//说明：优惠券 添加修改页面
	//********************************
	public function add(){

		$id = intval($_REQUEST['id']);
		if ($id>0) {
			$voucher = M('voucher')->where('id='.intval($id).' AND del=0')->find();
			$voucher['shop_name'] = M('shangchang')->where('id='.intval($voucher['shop_id']))->getField('name');
			$voucher['start_time'] = date("Y-m-d",$voucher['start_time']);
			$voucher['end_time'] = date("Y-m-d",$voucher['end_time']);

			//获取限定产品
			if ($voucher['proid']!='all' && $voucher['proid']!='') {
				$arr = explode(',', trim($voucher['proid'],','));
				foreach ($arr as $v) {
					$voucher['pro_list'][] = M('product')->where('id='.intval($v))->getField('photo_x');
				}
			}

			$this->assign('voucher',$voucher);
		}

		$this->display();
	}

	//********************************
	//说明：优惠券 添加修改
	//********************************
	public function save(){

		if (intval($_SESSION['admininfo']['qx'])!=4) {
			$shop_id = intval($_SESSION['admininfo']['shop_id']);
		}else{
			$shop_id = intval($_REQUEST['shop_id']);
		}

		$id=intval($_REQUEST['id']);
		$title = $_REQUEST['title'];
		$full_money = floatval($_REQUEST['full_money']);
		$amount = floatval($_REQUEST['amount']);
		$point = intval($_REQUEST['point']);
		$count = intval($_REQUEST['count']);
		$start_time = $_REQUEST['start_time'];
		$end_time = $_REQUEST['end_time'];
		$proid = trim($_REQUEST['proid'],',');
		if (!$proid) {
			$proid='all';
		}

		if (!$full_money) {
			$this->error('请输入满减金额.'.__LINE__);
			exit();
		}

		if (!$amount) {
			$this->error('请输入优惠金额.'.__LINE__);
			exit();
		}

		if (!$count || !$start_time || !$end_time) {
			$this->error('参数错误.');
			exit();
		}

		$data = array();
		$data['shop_id'] = $shop_id;
		$data['title'] = $title;
		$data['full_money'] = $full_money;
		$data['amount'] = $amount;
		$data['start_time'] = strtotime($start_time);
		$data['end_time'] = strtotime($end_time.' 23:59:59');
		$data['point'] = $point;
		$data['count'] = $count;
		$data['proid'] = $proid;
		if ($id>0) {
			$check = M('voucher')->where('id='.intval($id).' AND del=0')->find();
			if (intval($check['receive_num'])>0) {
				$this->error('优惠券已经生效，不能修改！');
				exit();
			}
			$res = M('voucher')->where('id='.intval($id))->save($data);
		}else{
			$data['addtime'] = time();
			$res = M('voucher')->add($data);
		}
		if ($res) {
			$this->success('操作成功！','index');
			exit();
		}else{
			$this->error('操作失败！');
			exit();
		}
	}

	//***************************
	//说明：优惠券 删除
	//***************************
	public function del(){
		$id = intval($_REQUEST['did']);
		$check_info = M('voucher')->where('id='.intval($id))->find();
		if (!$check_info) {
			echo '<script>alert("参数错误.");history.go(-1);</script>';
			exit();
		}

		if (intval($check_info['receive_num'])>0) {
			echo '<script>alert("优惠券已经生效，不能删除！");history.go(-1);</script>';
			exit();
		}

		//判断抢购产品是否已删除
		if (intval($check_info['del'])==1) {
			$this->success('操作成功！'.__LINE__,'index');
			exit();
		}

		$up = M('voucher')->where('id='.intval($id))->save(array('del'=>1));
		if ($up) {
			$this->success('操作成功！','index');
			exit();
		}else{
			echo '<script>alert("操作失败.");history.go(-1);</script>';
		    exit;
		}
	}

	//********************************
	//说明：获取产品列表
	//********************************
	public function get_pro(){
		$id=(int)$_GET['id'];

		//搜索变量
		$type=$this->htmlentities_u8($_GET['type']);
		$tuijian=$this->htmlentities_u8($_GET['tuijian']);
		$name=$this->htmlentities_u8($_GET['name']);

		//===========================================
		// 产品列表信息 搜索
		//===========================================
		$where="1=1 AND del<1";
		if (intval($_SESSION['admininfo']['qx'])!=4) {
			$shop_id = intval($_SESSION['admininfo']['shop_id']);
			if (!$shop_id) {
				echo "<script>alert('店铺状态异常！');</script>";
				exit();
			}
			$where .=' AND shop_id='.$shop_id;
		}
			
		$tuijian!=='' ? $where.=" AND type=$tuijian" : null;
		$name!='' ? $where.=" AND name like '%$name%'" : null;
		define('rows',20);
		$count=M('product')->where($where)->count();
		$rows=ceil($count/rows);
		$page=(int)$_GET['page'];
		$page<0?$page=0:'';
		$limit=$page*rows;
		$page_index=$this->page_index($count,$rows,$page);
		$productlist=M('product')->where($where)->order('addtime desc,id desc')->limit($limit,rows)->select();
		//dump($productlist);exit;
		foreach ($productlist as $k => $v) {
			$productlist[$k]['cat_name']= M('category')->where('id='.intval($v['cid']))->getField('name');
		}

		//==========================
		// 将GET到的数据再输出
		//==========================
		$this->assign('id',$id);
		$this->assign('tuijian',$tuijian);
		$this->assign('name',$name);
		$this->assign('type',$type);
		$this->assign('page',$page);
		//=============
		// 将变量输出
		$this->assign('productlist',$productlist);
		$this->assign('page_index',$page_index);
		$this->display();
	}

}