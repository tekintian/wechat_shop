<?php
namespace Api\Controller;
use Think\Controller;
class LoginController extends PublicController {

	//***************************
	//  前台登录接口
	//***************************
    public function dologin(){
		session_destroy();
		$name = trim($_POST['username']);	//接受“会员账号”
		$pwd  = pw_tmp_en($_POST['pwd']);	//接受“会员密码”
		if (!$name || !$pwd) {
			echo json_encode(array('status'=>0,'err'=>'请输入账号或密码！'));
			exit();
		}

		$user = M('user');
		$where['name'] = $name;
		$where['pwd'] = $pwd;
		$usrNum = $user->where($where)->find();
		///echo $user->_sql();exit;
		if($usrNum){
			@session_start();
			$_COOKIE['sessionid'] = session_id();
			//$_SESSION['sessionid']=session_id();
			$_SESSION['LoginCheck']=md5($name);
			$_SESSION['LoginName']=$name;
			$_SESSION['ID']=$usrNum['id'];
			$_SESSION['photo']=$usrNum['photo'];
			
			echo json_encode(array('status'=>1,'session'=>$_SESSION));
			exit();
		}else{
			echo json_encode(array('status'=>0,'err'=>'账号密码错误！'));
			exit();
		}		 
	}

	//***************************
	//  授权登录接口
	//***************************
	public function authlogin(){
		$openid = $_POST['openid'];
		if (!$openid) {
			echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
			exit();
		}
		$con = array();
		$con['openid']=trim($openid);
		$uid = M('user')->where($con)->getField('id');
		if ($uid) {
			$userinfo = M('user')->where('id='.intval($uid))->find();
			if (intval($userinfo['del'])==1) {
				echo json_encode(array('status'=>0,'err'=>'账号状态异常！'));
				exit();
			}
			$err = array();
			$err['ID'] = intval($uid);
			$err['NickName'] = $_POST['NickName'];
			$err['HeadUrl'] = $_POST['HeadUrl'];
			echo json_encode(array('status'=>1,'arr'=>$err));
			exit();
		}else{
			$data = array();
			$data['name'] = $_POST['NickName'];
			$data['uname'] = $_POST['NickName'];
			$data['photo'] = $_POST['HeadUrl'];
			$data['sex'] = $_POST['gender'];
			$data['openid'] = $openid;
			$data['source'] = 'wx';
			$data['addtime'] = time();
			if (!$data['openid']) {
				echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
				exit();
			}
			$res = M('user')->add($data);
			if ($res) {
				$err = array();
				$err['ID'] = intval($res);
				$err['NickName'] = $data['name'];
				$err['HeadUrl'] = $data['photo'];
				echo json_encode(array('status'=>1,'arr'=>$err));
				exit();
			}else{
				echo json_encode(array('status'=>0,'err'=>'授权失败！'.__LINE__));
				exit();
			}
		}
	}


	//***************************
	//  前台注册接口
	//***************************
  	public function register(){
	  	$name = trim($_POST['user']);
	  	$pwd  = pw_tmp_en($_POST['pwd']);
	    $pwds = pw_tmp_en($_POST['pwds']);
	   if($pwd!=$pwds) {
			echo json_encode(array('status'=>0,'err'=>'两次输入密码不同！'));
			exit();
		}

		$user=M('user');
		$where = array();
		$where['name']=$name;
		$count=$user->where($where)->count();
		if($count) {
			echo json_encode(array('status'=>0,'err'=>'用户名已被注册了！'));
			exit();
		}

		$check_mob=$user->where('tel='.trim($_POST['tel']))->count();
		if($check_mob) {
			echo json_encode(array('status'=>0,'err'=>'手机号已存在！'));
			exit();
		}
		$data = array();
		$data['name'] = $name;
        $data['qx'] = 6;

		$data['pwd']   = $pwd;
		$data['tel']   = trim($_POST['tel']);
		$data['addtime'] = time();
		$res = $user->add($data);
		if ($res) {
			$_SESSION['LoginName']=$name;
			$_SESSION['ID']=$res;
			$arr =array();
			$arr['status']=1;
			$arr['uid'] = $res;
			$arr['LoginName'] = $name;
			echo json_encode($arr);
			exit();
		}else{
			echo json_encode(array('status'=>0,'err'=>'注册失败！'));
			exit();
		}
	}

	//***************************
	//  获取sessionkey 接口
	//***************************
	public function getsessionkey(){
		$wx_config = C('weixin');
    	$appid = $wx_config['appid'];
    	$secret = $wx_config['secret'];

		$code = trim($_POST['code']);
		if (!$code) {
			echo json_encode(array('status'=>0,'err'=>'非法操作！'));
			exit();
		}

		if (!$appid || !$secret) {
			echo json_encode(array('status'=>0,'err'=>'非法操作！'.__LINE__));
			exit();
		}

		$get_token_url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$get_token_url);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$res = curl_exec($ch);  
		curl_close($ch);  
		echo $res;
		exit();
	}

	//***************************
	//  获取sessionkey 接口
	//***************************
	public function getsessionkeys(){
		$wx_config = C('weixin');
    	$appid = 'wx5e3dd81af8bf352a';
    	$secret = '30421d00aaee201bc0400994defbeed7';

		$code = trim($_POST['code']);
		if (!$code) {
			echo json_encode(array('status'=>0,'err'=>'非法操作！'));
			exit();
		}

		$get_token_url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$code.'&grant_type=authorization_code';
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$get_token_url);
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$res = curl_exec($ch);  
		curl_close($ch);  
		echo $res;
		exit();
	}

	//***************************
	//  前台退出登录接口
	//***************************
	public function logout(){
		unset($_SESSION['uid']);  
		unset($_SESSION['LoginName']);
		session_destroy();
		echo json_encode(array('status'=>1));
		exit();
	}
	
}