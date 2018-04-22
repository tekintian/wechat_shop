<?php
namespace Admin\Controller;
use Think\Controller;
class ProAttributeController extends PublicController{

	/*
	*
	* 构造函数，用于导入外部文件和公共方法
	*/
	public function _initialize(){
		$this->Attribute = M('attribute');
	}

	/*
	*
	* 获取、查询产品属性表数据
	*/
	public function index(){
		//搜索，根据产品属性名称搜索
		$attr_name = trim($_GET['attr_name']);
		$condition = array();
		if ($attr_name) {
			$condition['attr_name'] = array('LIKE','%'.$attr_name.'%');
			$this->assign('attr_name',$attr_name);
		}


		//分页
		$count   = $this->Attribute->where($condition)->count();// 查询满足要求的总记录数
		$Page    = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)

		//分页跳转的时候保证查询条件
		foreach($condition as $key=>$val) {
		    $Page->parameter[$key]  =  urlencode($val);
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
	    $Page->setConfig('theme', '%FIRST%%UP_PAGE%%LINK_PAGE%%DOWN_PAGE%%END%%HEADER%');

		$show  = $Page->show();// 分页显示输出

		$attr_list = $this->Attribute->where($condition)->order('sort desc,id desc')->limit($Page->firstRow.','.$Page->listRows)->select();		

		$this->assign('attr_list',$attr_list);
		$this->assign('page',$show);
		$this->display(); // 输出模板

	}


	/*
	*
	* 跳转添加或修改产品属性数据页面
	*/
	public function add(){
		//如果是修改，则查询对应分类信息
		if (intval($_GET['attr_id'])) {
			$attr_id = intval($_GET['attr_id']);
		
			$attr_info = $this->Attribute->where('id='.intval($attr_id))->find();
			if (!$attr_info) {
				$this->error('没有找到相关信息.');
			}
			$this->assign('attr_info',$attr_info);
		}
		$this->display();
	}


	/*
	*
	* 添加或修改产品属性
	*/
	public function save(){
		//添加或修改之前判断改属性是否已存在
		if (!intval($_POST['attr_id'])) {
			$check_attrid = $this->Attribute->where('attr_name="'.trim($_POST['attr_name']).'"')->getField('id');
			if ($check_attrid) {
				$this->error('该属性已添加.');
			}
		}

		//构建数组
		$this->Attribute->create();

		//保存数据
		if (intval($_POST['attr_id'])) {
			$result = $this->Attribute->where('id='.intval($_POST['attr_id']))->save();
		}else{
			//保存添加时间
			$this->Attribute->addtime = time();
			$result = $this->Attribute->add();
		}
		//判断数据是否更新成功
		if ($result) {
			$this->success('操作成功.','index');
		}else{
			$this->error('操作失败.');
		}
	}

	/*
	*
	* 添加或修改产品属性
	*/
	public function ajax_save(){
		//添加或修改之前判断改属性是否已存在
		$check_attrid = $this->Attribute->where('attr_name="'.trim($_POST['attrs_name']).'"')->getField('id');
		if ($check_attrid) {
			echo json_encode(array('status'=>0,'info'=>'该属性已添加.'));
			exit();
		}

		//构建数组
		$data = array();
		$data['attr_name'] = trim($_POST['attrs_name']);
		$data['addtime'] = time();
		$attr_id = $this->Attribute->add($data);
		//判断数据是否更新成功
		if ($attr_id) {
			echo json_encode(array('status'=>1,'attr_id'=>$attr_id));
			exit();
		}else{
			echo json_encode(array('status'=>0,'info'=>'操作失败.'));
			exit();
		}
	}

	/*
	*
	* ajax删除产品属性
	*/
	public function ajax_del(){
		//删除之前判断该属性是否还有其他
		$g_id = intval($_POST['g_id']);
		$pro_id = intval($_POST['pro_id']);
		$check_info = M('product')->where('id='.intval($pro_id))->getField('id');
		if (!$g_id || !$check_info) {
			echo json_encode(array('status'=>0,'info'=>'系统出了点小问题，请稍后再试.'));
			exit();
		}

		//获取规格对应属性id
		$check_id = M('guige')->where('id='.intval($g_id))->getField('attr_id');
		//执行删除
		$res = M('guige')->where('id='.intval($g_id))->delete();
		//判断数据是否更新成功
		if ($res) {
			//判断规格表里是否还有同属性的规格
			$check_info = M('guige')->where('attr_id='.intval($check_id).' AND pid='.intval($pro_id))->find();
			if (!$check_info) {
				$pro_buff = M('product')->where('id='.intval($pro_id))->getField('pro_buff');
				$buff = explode(',', $pro_buff);
				if ($buff) {
					$key = array_search($check_id, $buff);
					array_splice($buff,$key,1);
					$bb = implode(',', $buff);
					M('product')->where('id='.intval($pro_id))->save(array('pro_buff'=>$bb));
				}
			}
			$this->set_pro_attr($pro_id);

			echo json_encode(array('status'=>1));
			exit();
		}else{
			echo json_encode(array('status'=>0,'info'=>'删除失败.'));
			exit();
		}
	}


	/*
	*
	* 添加或修改产品属性
	*/
	public function save_attr(){
		//构建数组
		$pro_id = intval($_POST['pid']);
		$check_id = M('pro_attr')->where('pid='.intval($pro_id))->getField('id');

		$d = array();
		foreach ($_POST['gg_name'] as $k => $v) {
			$d[$k]['gg_name'] = $v;
		}

		foreach ($_POST['price'] as $k => $v) {
			$d[$k]['price'] = $v;
		}

		foreach ($_POST['stock'] as $k => $v) {
			$d[$k]['stock'] = $v;
		}

		$data = array();
		$data['name'] = serialize($d);
		$data['sotr'] = 1;
		if ($check_id) {
			$up = M('pro_attr')->where('id='.intval($check_id))->save($data);
		}else{
			$data['pid'] = $pro_id;
			$data['addtime'] = time();
			$up = M('pro_attr')->add($data);
		}

		//判断数据是否更新成功
		if ($up) {
			$this->success('保存成功.');
			exit();
		}else{
			$this->error('操作失败.');
			exit();
		}
	}


	/*
	*
	* 产品属性删除
	*/
	public function del(){
		//以后删除还要加权限登录判断
		$id = intval($_GET['did']);
		if (!$id) {
			$this->error('非法操作.');
		}
		//判断该分类下是否还有子分类
		$check_id = $this->Attribute->where('id='.intval($id))->find();
		if (!$check_id) {
			$this->error('系统繁忙，请时候再试！');
		}

		//判断该产品属性用户是否正在使用
		$check_attr = M('Guige')->where('attr_id='.intval($id))->getField('id');
		if ($check_attr) {
			$this->error('该产品属性商家正在使用中，不能删除！');
		}

		$res = $this->Attribute->where('id='.intval($id))->delete();
		if ($res) {
			$this->success('操作成功.');
		}else{
			$this->error('操作失败.');
		}
	}

	//**********************************************
	//说明：设置单产品不同规格不同价格不同库存 公共方法
	//**********************************************
	public function set_pro_attr($pro_id){
		$pro_info = M('product')->where('id='.intval($pro_id))->field('price_yh,num')->find();

		//获取产品所有规格属性组合，没有就添加
		$proAttrid = M('pro_attr')->where('pid='.intval($pro_id))->getField('id');
		//遍历查询到的属性名称
		$d = array();$pro_buff=array();
		$buff = M('product')->where('id='.intval($pro_id))->getField('pro_buff');
		$pro_buff = explode(',', $buff);
		foreach ($pro_buff as $k => $v) {
			$a = M('guige')->where('pid='.intval($pro_id).' AND attr_id='.intval($v))->field('name')->select();
			foreach ($a as $key => $val) {
				$b[$k][] = $val['name'];
			}
		}
		//组合所有规格属性
		foreach ($b[0] as $k => $v) {
			if ($b[1]) {
				foreach ($b[1] as $k1 => $v1) {
					if ($b[2]) {
						foreach ($b[2] as $k2 => $v2) {
							if ($b[3]) {
								foreach ($b[3] as $k3 => $v3) {
									$d[] = $v.','.$v1.','.$v2.','.$v3;
								}
							}else{
								$d[] = $v.','.$v1.','.$v2;
							}
						}
					}else{
						$d[] = $v.','.$v1;
					}
				}
			}else{
				$d[] = $v;
			}
		}
		//把所有组合存入一个数组
		$arr=array();$arr1=array();
		foreach ($d as $k => $v) {
			$arr1['gg_name'] = $v;
			$arr1['price'] = $pro_info['price_yh'];//价格：默认为添加产品时的优惠价格
			$arr1['stock'] = $pro_info['num'];//库存：默认为添加产品时的数量
			$arr[] = $arr1;
		}
		//存入产品属性规格表
		$data = array();
		$data['pid'] = intval($pro_id);
		$data['name'] = serialize($arr);
		$data['addtime'] = time();
		if ($proAttrid) {
			$res = M('pro_attr')->where('id='.intval($proAttrid))->save($data);
		}else{
			$res = M('pro_attr')->add($data);
		}
	}

	public function test(){
		if (IS_POST) {
			try {
				if ($_POST['attribute']) {
					$attribute = $_POST['attribute'];
					foreach ($attribute as $k => $v) {
						$guige_name = $_POST['guige_name'][$v];
						if ($guige_name) {
							foreach ($guige_name as $key => $val) {
								$data=array();
								$data['pid'] = 1;
								$data['attr_id'] = $v;
								$data['name'] = $val;
								$data['price'] = 0;
								$res = M('guige')->add($data);
								if (!$res) {
									throw new Exception('产品属性添加失败.');
								}
							}
						}
					}
				}
			} catch (Exception $e) {
				$this->error($e->getMessage());
			}
			$this->success('操作成功.');
			exit();
			
		}

		$attr_list = $this->Attribute->select();
		$this->assign('attr_list',$attr_list);
		$this->display();	
	}

	public function test2(){
		$string = array();
		$string['color'][]='红色';
		$string['color'][]='黑色';
		$string['color'][]='黄色';
		$string['color'][]='蓝色';
		$string['size'][]='M';
		$string['size'][]='L';
		$string['size'][]='XL';
		$string['type'][]='长袖加绒';
		$string['type'][]='长袖单';
		$string['type'][]='短袖';

		$d = array();
		foreach ($string['color'] as $i => $_a ){
			foreach ($string['size'] as $ii => $_b ){
				foreach ($string['type'] as $iii => $_c ){
					$d[] = $_a.','.$_b.','.$_c;
				}
			}
		}

		print_r($d);die();

		$this->assign('string',$string);
		$this->display();
	}

}