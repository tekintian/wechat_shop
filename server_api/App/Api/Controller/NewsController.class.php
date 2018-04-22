<?php
// 本类由系统自动生成，仅供测试用途
namespace Api\Controller;
use Think\Controller;
class NewsController extends PublicController {
    //*****************************
    //  新闻列表
    //*****************************
    public function index(){
        $keyword=$_POST['keyword'];
        $where = '1=1';
        if ($keyword) {
            $where .=' AND name LIKE "%'.$keyword.'%"';
        }

        $list = M('news')->where($where)->field('id,cid,digest,name,photo,addtime,source')->order('sort desc,addtime desc')->limit(8)->select();
        foreach ($list as $k => $v) {
            $list[$k]['photo']=__DATAURL__.$v['photo'];
            $list[$k]['cname'] = M('news_cat')->where('id='.intval($v['cid']))->getField('name');
            $list[$k]['addtime']=date('Y-m-d',$v['addtime']);
        }
        //json加密输出
        //dump($json);
        echo json_encode(array('list'=>$list));
        exit();
    }

    //*****************************
    //  新闻列表  加载更多
    //*****************************
    public function getlist(){
        $page = intval($_REQUEST['page']);
        if (!$page) {
            $page = 2;
        }
        $limit = $page*8-8;

        $list = M('news')->where($where)->field('id,cid,digest,name,photo,addtime,source')->order('sort desc,addtime desc')->limit($limit.',8')->select();
        foreach ($list as $k => $v) {
            $list[$k]['photo']=__DATAURL__.$v['photo'];
            $list[$k]['cname'] = M('news_cat')->where('id='.intval($v['cid']))->getField('name');
            $list[$k]['addtime']=date('Y-m-d',$v['addtime']);
        }
        //json加密输出
        //dump($json);
        echo json_encode(array('list'=>$list));
        exit();
    }

    //*****************************
    //  新闻详情
    //*****************************
    public function detail(){
        $newid=intval($_REQUEST['news_id']);
        $detail=M('news')->where('id='.intval($newid))->find();
        if (!$detail) {
            echo json_encode(array('status'=>0,'err'=>'没有找到相关信息.'));
            exit();
        }

        $up = array();
        $up['click'] = intval($detail['click'])+1;
        M('news')->where('id='.intval($newid))->save($up);

        $content = str_replace('/minipetmrschool/Data/', __DATAURL__, $detail['content']);
        $detail['content']=html_entity_decode($content, ENT_QUOTES, "utf-8");

        $detail['addtime'] = date("Y-m-d",$detail['addtime']);

        echo json_encode(array('status'=>1,'info'=>$detail));
        exit();
    }
    
}