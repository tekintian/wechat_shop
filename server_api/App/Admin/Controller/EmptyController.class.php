<?php
namespace Admin\Controller;
use Think\Controller;
class EmptyController extends PublicController{
	public function index(){
		//输出百度地图插件的路径
		if(CONTROLLER_NAME =="Baidumap"){
			$path=__ROOT__."/App/Admin/View/Baidumap/";
			$this->assign('path',$path);
		}
		$this->display();
	}	
}