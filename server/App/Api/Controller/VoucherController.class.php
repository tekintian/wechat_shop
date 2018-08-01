<?php
namespace Api\Controller;
use Think\Controller;
class VoucherController extends PublicController {
	//***************************
	//  所有单页数据接口
	//***************************
    public function index(){
    	$condition = array();
        $condition['del'] = 0;
        $condition['start_time'] = array('lt',time());
        $condition['end_time'] = array('gt',time());

        $vou = M('voucher')->where($condition)->order('addtime desc')->select();
        foreach ($vou as $k => $v) {
            $vou[$k]['start_time'] = date("Y.m.d",intval($v['start_time']));
            $vou[$k]['end_time'] = date("Y.m.d",intval($v['end_time']));
            $vou[$k]['amount'] = floatval($v['amount']);
            $vou[$k]['full_money'] = floatval($v['full_money']);
            if ($v['proid']=='all' || empty($v['proid'])) {
                $vou[$k]['desc'] = '店内通用';
            }else{
                $vou[$k]['desc'] = '限定商品';
            }
        }
        echo json_encode(array('status'=>1,'vou'=>$vou));
        exit();
    }

    //***************************
    //  用户领取优惠券
    //***************************
    public function get_voucher(){
        $vid = intval($_REQUEST['vid']);
        $uid = intval($_REQUEST['uid']);
        $check_user = M('user')->where('id='.intval($uid).' AND del=0')->find();
        if (!$check_user) {
            echo json_encode(array('status'=>0,'err'=>'登录状态异常！err_code:'.__LINE__));
            exit();
        }

        $check_vou = M('voucher')->where('id='.intval($vid).' AND del=0')->find();
        if (!$check_vou) {
            echo json_encode(array('status'=>0,'err'=>'优惠券信息错误！err_code:'.__LINE__));
            exit();
        }

        //判断是否已领取过
        $check = M('user_voucher')->where('uid='.intval($uid).' AND vid='.intval($vid))->getField('id');
        if ($check) {
            echo json_encode(array('status'=>0,'err'=>'您已经领取过了！'));
            exit();
        }

        if (intval($check_vou['point'])!=0 && intval($check_vou['point'])>intval($check_user['jifen'])) {
            echo json_encode(array('status'=>0,'err'=>'积分余额不足！'));
            exit();
        }

        if ($check_vou['start_time']>time()) {
            echo json_encode(array('status'=>0,'err'=>'优惠券还未生效！'));
            exit();
        }

        if ($check_vou['end_time']<time()) {
            echo json_encode(array('status'=>0,'err'=>'优惠券已失效！'));
            exit();
        }

        if (intval($check_vou['count'])<=intval($check_vou['receive_num'])) {
            echo json_encode(array('status'=>0,'err'=>'优惠券已被领取完了！'));
            exit();
        }

        $data = array();
        $data['uid'] = $uid;
        $data['vid'] = $vid;
        $data['shop_id'] = intval($check_vou['shop_id']);
        $data['full_money'] = floatval($check_vou['full_money']);
        $data['amount'] = floatval($check_vou['amount']);
        $data['start_time'] = $check_vou['start_time'];
        $data['end_time'] = $check_vou['end_time'];
        $data['addtime'] = time();
        $res = M('user_voucher')->add($data);
        if ($res) {
            //修改会员积分
            if (intval($check_vou['point'])!=0) {
                $arr = array();
                $arr['jifen'] = intval($check_user['jifen'])-intval($check_vou['point']);
                $up = M('user')->where('id='.intval($uid))->save($arr);
            }

            //修改领取数量
            $arrs = array();
            $arrs['receive_num'] = intval($check_vou['receive_num'])+1;
            $ups = M('voucher')->where('id='.intval($vid))->save($arrs);
            
            echo json_encode(array('status'=>1));
            exit();
        }else{
            echo json_encode(array('status'=>0,'err'=>'领取失败！'));
            exit();
        }
    }
}