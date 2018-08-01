<?php
namespace Api\Controller;
use Think\Controller;
class SearchController extends PublicController {
	//***************************
	//  获取会员 搜索记录接口
	//***************************
    public function index(){
    	$uid = intval($_REQUEST['uid']);
    	//获取热门搜索内容
        $remen = M('search_record')->group('keyword')->field('keyword')->order('SUM(num) desc')->limit(10)->select();
        //获取历史搜索记录
        $history = array();
        if ($uid) {
            $history = M('search_record')->where('uid='.intval($uid))->order('addtime desc')->field('keyword')->limit(20)->select();
        }
        echo json_encode(array('remen'=>$remen,'history'=>$history));
        exit();
    }

    //***************************
    //  产品商家搜索接口
    //***************************
    public function searches(){
        $uid = intval($_REQUEST['uid']);

        $keyword = trim($_REQUEST['keyword']);
        if (!$keyword) {
            echo json_encode(array('status'=>0,'err'=>'请输入搜索内容.'));
            exit();
        }

        if ($uid) {
            $check = M('search_record')->where('uid='.intval($uid).' AND keyword="'.$keyword.'"')->find();
            if ($check) {
               $num = intval($check['num'])+1;
               M('search_record')->where('id='.intval($check['id']))->save(array('num'=>$num));
            }else{
               $add = array();
               $add['uid'] = $uid;
               $add['keyword'] = $keyword;
               $add['addtime'] = time();
               M('search_record')->add($add);
            }
        }

        $page=intval($_REQUEST['page']);
        if (!$page) {
            $page=0;
        }

        $prolist = M('product')->where('del=0 AND pro_type=1 AND is_down=0 AND name LIKE "%'.$keyword.'%"')->order('addtime desc')->field('id,name,photo_x,shiyong,price,price_yh')->select();
        foreach ($prolist as $k => $v) {
            $prolist[$k]['photo_x'] = __DATAURL__.$v['photo_x'];
        }

        $page2=intval($_REQUEST['page2']);
        if (!$page2) {
            $page2=0;
        }

        $condition = array();
        $condition['status']=1;
        //根据店铺名称查询
        $condition['name']=array('LIKE','%'.$keyword.'%');
        //获取所有的商家数据
        $store_list = M('shangchang')->where($condition)->order('sort desc,type desc')->field('id,name,uname,logo,tel,sheng,city,quyu')->limit($page2.',6')->select();
        foreach ($store_list as $k => $v) {
            $store_list[$k]['sheng'] = M('china_city')->where('id='.intval($v['sheng']))->getField('name');
            $store_list[$k]['city'] = M('china_city')->where('id='.intval($v['city']))->getField('name');
            $store_list[$k]['quyu'] = M('china_city')->where('id='.intval($v['quyu']))->getField('name');
            $store_list[$k]['logo'] = __DATAURL__.$v['logo'];
            $pro_list = M('product')->where('del=0 AND is_down=0 AND shop_id='.intval($v['id']))->field('id,photo_x,price_yh')->limit(4)->select();
            foreach ($pro_list as $key => $val) {
                $pro_list[$key]['photo_x'] = __DATAURL__.$val['photo_x'];
            }
            $store_list[$k]['pro_list'] = $pro_list;
        }

        echo json_encode(array('status'=>1,'pro'=>$prolist,'shop'=>$store_list));
        exit();
    }


}