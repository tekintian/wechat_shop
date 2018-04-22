<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends PublicController {
	//***************************
	//  首页数据接口
	//***************************
    public function index(){
    	//如果缓存首页没有数据，那么就读取数据库
    	/***********获取首页顶部轮播图************/
    	$ggtop=M('guanggao')->order('sort desc,id asc')->field('id,name,photo')->limit(10)->select();
		foreach ($ggtop as $k => $v) {
			$ggtop[$k]['photo']=__DATAURL__.$v['photo'];
			$ggtop[$k]['name']=urlencode($v['name']);
		}
    	/***********获取首页顶部轮播图 end************/

        //======================
        //首页推荐品牌 20个
        //======================
        $brand = M('brand')->where('1=1')->field('id,name,photo')->limit(20)->select();
        foreach ($brand as $k => $v) {
            $brand[$k]['photo'] = __DATAURL__.$v['photo'];
        }

        //======================
        //首页培训课程
        //======================
        $course = M('course')->where('del=0')->order('id desc')->field('id,title,intro,photo')->select();
        foreach ($course as $k => $v) {
            $course[$k]['photo'] = __DATAURL__.$v['photo'];
        }

    	//======================
    	//首页推荐产品
    	//======================
    	$pro_list = M('product')->where('del=0 AND pro_type=1 AND is_down=0 AND type=1')->order('sort desc,id desc')->field('id,name,intro,photo_x,price_yh,price,shiyong')->limit(8)->select();
    	foreach ($pro_list as $k => $v) {
    		$pro_list[$k]['photo_x'] = __DATAURL__.$v['photo_x'];
    	}

        //======================
        //首页分类 自己组建数组
        //======================
        $indeximg = M('indeximg')->where('1=1')->order('id asc')->field('photo')->select();
        $procat = array();
        $procat[0]['name'] = '新闻资讯';
        $procat[0]['imgs'] = __DATAURL__.$indeximg[0]['photo'];
        $procat[0]['link'] = 'other';
        $procat[0]['ptype'] = 'news';

        $procat[1]['name'] = '教学优势';
        $procat[1]['imgs'] = __DATAURL__.$indeximg[1]['photo'];
        $procat[1]['link'] = 'other';
        $procat[1]['ptype'] = 'jxys';

        $procat[2]['name'] = '学员风采';
        $procat[2]['imgs'] = __DATAURL__.$indeximg[2]['photo'];
        $procat[2]['link'] = 'other';
        $procat[2]['ptype'] = 'xyfc';

        $procat[3]['name'] = '关于我们';
        $procat[3]['imgs'] = __DATAURL__.$indeximg[3]['photo'];
        $procat[3]['link'] = 'other';
        $procat[3]['ptype'] = 'gywm';

    	echo json_encode(array('ggtop'=>$ggtop,'procat'=>$procat,'prolist'=>$pro_list,'brand'=>$brand,'course'=>$course));
    	exit();
    }

    //***************************
    //  首页产品 分页
    //***************************
    public function getlist(){
        $page = intval($_REQUEST['page']);
        $limit = intval($page*8)-8;

        $pro_list = M('product')->where('del=0 AND pro_type=1 AND is_down=0 AND type=1')->order('sort desc,id desc')->field('id,name,photo_x,price_yh,shiyong')->limit($limit.',8')->select();
        foreach ($pro_list as $k => $v) {
            $pro_list[$k]['photo_x'] = __DATAURL__.$v['photo_x'];
        }

        echo json_encode(array('prolist'=>$pro_list));
        exit();
    }

    public function ceshi(){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;

        for($i=0;$i<32;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

        echo $str;
    }

}