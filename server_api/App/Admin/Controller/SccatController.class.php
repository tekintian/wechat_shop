<?php
namespace Admin\Controller;
use Think\Controller;
class SccatController extends PublicController{

	/*
	*
	* 构造函数，用于导入外部文件和公共方法
	*/
	public function _initialize(){
		$this->Brand = D('sccat');
	}

	/*
	*
	* 获取、查询品牌数据
	*/
	public function index(){
		//搜索，根据广告标题搜索
		$brand_name = intval($_REQUEST['name']);
		$condition = array();
		if ($brand_name) {
			$condition['name'] = array('LIKE','%'.$brand_name.'%');
			$this->assign('name',$brand_name);
		}

		//分页
		$count   = $this->Brand->where($condition)->count();// 查询满足要求的总记录数
		$Page    = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)

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

		$list = $this->Brand->where($condition)->limit($Page->firstRow.','.$Page->listRows)->select();		

		$this->assign('list',$list);
		$this->assign('page',$show);
		$this->display(); // 输出模板

	}


	/*
	*
	* 跳转添加或修改品牌数据页面
	*/
	public function add(){
		//如果是修改，则查询对应广告信息
		if (intval($_GET['id'])) {
			$id = intval($_GET['id']);
		
			$brand_info = $this->Brand->where('id='.intval($id))->find();
			$this->assign('brand_info',$brand_info);
		}
		$this->display();
	}


	/*
	*
	* 添加或修改品牌信息
	*/
	public function save(){
		//构建数组
		$name = trim($_POST['name']);
		if (empty($name)) {
			$this->error('请输入分类名称.');
			exit();
		}

		//保存数据
		if (intval($_POST['id'])) {
			$check = $this->Brand->where('name="'.$name.'" AND id!='.intval($_POST['id']))->getField('id');
			if ($check) {
				$this->error('分类名称已存在.');
				exit();
			}
			$result = $this->Brand->where('id='.intval($_POST['id']))->save(array('name'=>$name));
		}else{
			//保存添加时间
			$check = $this->Brand->where('name="'.$name.'"')->getField('id');
			if ($check) {
				$this->error('分类名称已存在.');
				exit();
			}
			$add = array();
			$add['name'] = $name;
			$add['addtime'] = time();
			$result = $this->Brand->add($add);
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
	* 品牌删除
	*/
	public function del(){
		//获取广告id，查询数据库是否有这条数据
		$id = intval($_REQUEST['did']);
		$check_info = $this->Brand->where('id='.intval($id))->find();
		if (!$check_info) {
			$this->error('参数错误！');
			die();
		}

		//修改对应的显示状态
		$up = $this->Brand->where('id='.intval($id))->delete();
		if ($up) {
			$this->success('操作成功.','index');
		}else{
			$this->error('操作失败.');
		}
	}

}