<?php
namespace Home\Model;
use Think\Model;
class NewsModel extends Model {
	  public function _after_select(&$data,$option){
	  	//dump(1);exit;
	  	  foreach ($data as $k => $v) {
	  	  	$data[$k]['pid']=M('category')->where("id='".$v['pid']."'")->getField('name');
	  	  }
	  }
}