<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class PublicController extends Controller {
    
    //构造函数
    public function _initialize(){
	    //php 判断http还是https
    	$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://'; 
    	//所有图片路径
	    define(__DATAURL__, $http_type.$_SERVER['SERVER_NAME'].__DATA__.'/');
	    define(__PUBLICURL__, $http_type.$_SERVER['SERVER_NAME'].__PUBLIC__.'/');
	    define(__HTTP__, $http_type);
    }
   
    //查找二级分类下的所有子分类id，用逗号拼接
    public function catid_tree($id=2){
		$Category = M('category');
		$list=$Category->where("tid=".$id)->order('sort desc,id asc')->select();
		//dump($list);exit;
		$cidstr='';
		foreach($list as $v){
			$json[]=$v['id'];
			$num=$Category->where("tid=".$v['id'])->field('id')->count();
			if($num>0){
				$json[]=$this->catid_tree($v['id']);
			}
		}
		$cidstr.=implode(',',$json);
		return $cidstr;		
	}
	//一次性查出产品分类的所有分类
	public function cat_tree($id=2){
		$Category = M('category');
		$list=$Category->where("tid=".$id)->field('id,tid,name')->order('sort desc,id asc')->select();
		//echo '<pre>';print_r($list);exit;
		foreach($list as $v){
			$num = $Category->where("tid=".$v['id'])->count();
			$subclass=array();
			if($num>0)
			{
				$subclass=$this->cat_tree($v['id']);
			}
			$json[]=array(
				'id' => $v['id'] ,
				'name' => $v['name'] ,
				'num' => $num ,
				'subclass' => $subclass,
			);
		}
		return $json;		
	}
	//导航部分  查找父级分类
    function getAllFcateIds($categoryID)
    {
        //初始化ID数组
        $array[] = $categoryID;
         
        do
        {
            $ids = '';
            $where['id'] = array('in',$categoryID);
            $cate = M('category')->where($where)->field('id,tid,name')->select();
           // echo M('aaa_cpy_category')->_sql();
            foreach ($cate as $v)
            {
                $array[] = $v['tid'];
                $ids .= ',' . $v['tid'];
            }
            $ids = substr($ids, 1, strlen($ids));
            $categoryID = $ids;
        }
        while (!empty($cate));
       // $cates=array();
        foreach ($array as $key=>$va){
           $cates[] = M('category')->where('id='.$va)->field('id,tid,name')->find();
          // echo M('aaa_cpy_category')->_sql();
		  //echo $cates[$key]['name'];
		   $cates[$key]['name']=str_replace('（系统分类，不要删除）','',$cates[$key]['name']);
        }
        array_pop($cates);
        $ca=array_reverse($cates);
		//echo "<pre>";
	   // print_r($ca);
        return $ca; //返回数组
    }
    
	public function ispc($val){
		//$val = 1850;//这个为admin_app的id
		$app = M('admin_app');
		$val=$app->getField('id');
		//$url = $app->db(2,DB)->where('id='.$val)->field('ispcshop,end_time,name,pcnav_color,ahover_color')->find();
		$url = $app->where('id='.$val)->field('ispcshop,end_time,name,pcnav_color,ahover_color')->find();
		//print_r($url);exit;
		//return $url;
		
		if($url['end_time'] > time()){
			return $url;
		}else{
			return 0;
		}
    }
	
	
}