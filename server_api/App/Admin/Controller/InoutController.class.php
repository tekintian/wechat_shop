<?php
namespace Admin\Controller;
use Think\Controller;
class InoutController extends Controller {
     public function _initialize(){
        //订单状态获取
        $this->order_status = array('10'=>'待付款','20'=>'待发货','30'=>'待收货','40'=>'交易关闭','50'=>'交易完成');
        //支付类型获取
        $this->pay_type = array('alipay'=>'支付宝','weixin'=>'微信支付','cash'=>'现金支付');
        //退款状态获取
        $this->back_status = array('0'=>'','1'=>'退款中','2'=>'已退款');

     }
    public function index() {
        //$this->assign('current',1);
        $this->display();
    }

    public function exportExcel($expTitle,$expCellName,$expTableData){
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $expTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);

        vendor("PHPExcel.PHPExcel");
            
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
            }
        }

        /*$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(100);*/
        foreach ($cellName as $k) {
            if ($k!='A') {
                $objPHPExcel->getActiveSheet()->getColumnDimension($k)->setWidth(15);
            }
        }

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /**
     *
     * 导出Excel
     */
    public function expOrder(){//导出Excel
        /*if(session('userid')!=1){
            $this->error('此操作需要超级管理员权限！',U('Index/contacts'),2);
        }*/
        $xlsName  = "订单";//导出的文件名
        //要导出的信息
        $xlsCell  = array(
        array('id','订单ID'),
        array('shop_id','商家名称'),
        array('uid','买家'),
        array('price','总金额'),
        array('type','支付类型'),
        array('status','订单状态'),
        array('addtime','订单时间'),
        array('back','退款状态'),
        array('remark','买家留言'),
        array('receiver','收货人'),
        array('tel','联系方式'),
        array('address_xq','收货地址信息')
        );

        //根据查询条件导出订单
        //获取商家id
        if (intval($_SESSION['admininfo']['qx'])!=4) {
            $shop_id = intval(M('adminuser')->where('id='.intval($_SESSION['admininfo']['id']))->getField('shop_id'));
            if ($shop_id==0) {
                $this->error('非法操作.');
            }
        }else{
            $shop_id = intval($_REQUEST['shop_id']);
        }
        
        $pay_type = trim($_GET['pay_type']);//支付类型
        $pay_status = intval($_GET['pay_status']); //订单状态
        $start_time = intval(strtotime($_GET['start_time'])); //订单状态
        $end_time = intval(strtotime($_GET['end_time'])); //订单状态
        $where = '1=1';
        //根据商家id搜索
        if ($shop_id) {
            $where .=' AND shop_id='.intval($shop_id);
        }
        //根据支付类型搜索
        if ($pay_type) {
            $where .=' AND type='.$pay_type;
        }
        //根据订单状态搜索
        if ($pay_status) {
            if ($pay_status<10) {
                //小于10的为退款
                $where .=' AND back='.intval($pay_status);
            }else{
                //大于10的为正常订单
                $where .=' AND status='.intval($pay_status);
            }
        }
        //根据下单时间搜索
        if ($start_time) {
            $where .=' AND addtime>'.$start_time;
        }
        //根据下单时间搜索
        if ($end_time) {
            $where .=' AND addtime<'.$end_time;
        }
        //查询要导出的信息
        $xlsModel = M('order');
        $xlsData  = $xlsModel->where($where)->Field('id,shop_id,uid,price,type,status,addtime,back,remark,receiver,tel,address_xq')->order('id desc')->select();
        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['shop_id']=M('shangchang')->where('id='.intval($v['shop_id']))->getField('name');
            $xlsData[$k]['uid']=M('user')->where('id='.intval($v['uid']))->getField('name');
            $xlsData[$k]['type']=$this->pay_type[$v['type']];
            $xlsData[$k]['status']=$this->order_status[$v['status']];
            $xlsData[$k]['addtime']=date('Y-m-d H:i:s',$v['addtime']);
            $xlsData[$k]['back']=$this->back_status[$v['back']];
        }
        //print_r($where);die();
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
            
    }
    /**
     *
     * 导出会员信息Excel
     */
    public function expUser(){//导出Excel
        /*if(session('userid')!=1){
            $this->error('此操作需要超级管理员权限！',U('Index/contacts'),2);
        }*/
        $xlsName  = "会员信息";//导出的文件名
        //要导出的信息
        $xlsCell  = array(
        array('id','会员ID'),
        array('name','登录账号'),
        array('uname','昵称'),
        array('qx','权限'),
        array('addtime','注册时间'),
        array('tel','手机'),
        array('qq_id','QQ'),
        array('email','电子邮箱'),
        array('sex','性别')
        );

        //根据查询条件导出订单
        $tel = $_GET['tel'];    //手机
        $name = $_GET['name'];//账号名
        $where = '1=1 AND del<1';
        //根据手机号搜索
        if ($tel) {
            $where .=' AND tel='.$tel;
        }
        //根据支付类型搜索
        if ($name) {
            $where .=' AND name='.$name;
        }

        //查询要导出的信息
        $xlsModel = M('user');
        $xlsData  = $xlsModel->where($where)->Field('id,name,uname,qx,addtime,tel,qq_id,email,sex')->order('id desc')->select();
        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['qx']="普通会员";
            $xlsData[$k]['addtime']=date('Y-m-d H:i:s',$v['addtime']);
            $xlsData[$k]['sex']=$xlsData[$k]['sex']!=1 ? "男" : "女";
        }
        //print_r($where);die();
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
            
    }
    /**
     *
     * 导出商家会员信息Excel
     */
    public function expShopuser(){//导出Excel
        /*if(session('userid')!=1){
            $this->error('此操作需要超级管理员权限！',U('Index/contacts'),2);
        }*/
        $xlsName  = "商家会员信息";//导出的文件名
        //要导出的信息
        $xlsCell  = array(
        array('id','会员ID'),
        array('name','登录账号'),
        array('uname','昵称'),
        array('qx','权限'),
        array('addtime','注册时间'),
        array('tel','手机'),
        array('qq_id','QQ'),
        array('email','电子邮箱'),
        array('sex','性别')
        );

        //根据查询条件导出订单
        $tel = intval($_GET['tel']);    //手机
        $name = trim($_GET['name']);//账号名
        $where = '1=1';
        //根据手机号搜索
        if ($tel) {
            $where .=' AND tel='.intval($tel);
        }
        //根据支付类型搜索
        if ($name) {
            $where .=' AND name='.$name;
        }
      
        //查询要导出的信息
        $xlsModel = M('user');
        $xlsData  = $xlsModel->where($where)->Field('id,name,uname,qx,addtime,tel,qq_id,email,sex')->order('id desc')->select();
        foreach ($xlsData as $k => $v)
        {
            $xlsData[$k]['qx']="普通会员";
            $xlsData[$k]['addtime']=date('Y-m-d H:i:s',$v['addtime']);
            $xlsData[$k]['sex']=$xlsData[$k]['sex']!=1 ? "男" : "女";
        }
        //print_r($where);die();
        $this->exportExcel($xlsName,$xlsCell,$xlsData);
            
    }

    public function expTest() {
        //调用excel扩展
        vendor("PHPExcel.PHPExcel");
        vendor("PHPExcel.PHPExcel.Writer.Excel2007");
        vendor("PHPExcel.PHPExcel.Writer.Excel5");
        vendor("PHPExcel.PHPExcel.IOFactory");
        vendor("PHPExcel.PHPExcel.Cell");

        $objExcel = new \PHPExcel();
        //设置属性 (这段代码无关紧要，其中的内容可以替换为你需要的)
        $objExcel->getProperties()->setCreator("Leren");
        $objExcel->getProperties()->setLastModifiedBy("Leren");
        $objExcel->getProperties()->setTitle("Office 2003 XLS Test Document");
        $objExcel->getProperties()->setSubject("Office 2003 XLS Test Document");
        $objExcel->getProperties()->setDescription("Test document for Office 2003 XLS, generated using PHP classes.");
        $objExcel->getProperties()->setKeywords("office 2003 openxml php");
        $objExcel->getProperties()->setCategory("Test result file");
        $objExcel->setActiveSheetIndex(0);

            //echo $where;exit;
            //获取要导出的数据
            $links_list = M('link')->select();
            //print_r($links_list);exit;
            $ex = '';
            $i=0;
            //表头
            $k1="测试ID";
            $k2="测试名称";
            $k3="测试3";
            $k4="测试4";

            $objExcel->getActiveSheet()->setCellValue('a1', "$k1");
            $objExcel->getActiveSheet()->setCellValue('b1', "$k2");
            $objExcel->getActiveSheet()->setCellValue('c1', "$k3");
            $objExcel->getActiveSheet()->setCellValue('d1', "$k4");

            $num = count($links_list);
            foreach($links_list as $k=>$v) {
                $u1=$i+2;
                //print_r(select('uname','aaa_pts_user','id='.$v['uid']));exit;
                /*----------写入内容-------------*/
                $objExcel->getActiveSheet()->setCellValue('a'.$u1, $v["id"]);
                $objExcel->getActiveSheet()->setCellValue('b'.$u1, $v['name']);
                $objExcel->getActiveSheet()->setCellValue('c'.$u1, $v['link']);
                $objExcel->getActiveSheet()->setCellValue('d'.$u1, $v['sort']);
                $i++;
            }
            // 高置列的宽度
            //$objExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
            //$objExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
            $objExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
            //$objExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);

            $objExcel->getActiveSheet()->getHeaderFooter()->setOddHeader('&L&BPersonal cash register&RPrinted on &D');
            $objExcel->getActiveSheet()->getHeaderFooter()->setOddFooter('&L&B' . $objExcel->getProperties()->getTitle() . '&RPage &P of &N');

            // 设置页方向和规模
            $objExcel->getActiveSheet()->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
            $objExcel->getActiveSheet()->getPageSetup()->setPaperSize(\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
            $objExcel->setActiveSheetIndex(0);
            $timestamp = '导出表'.date("_YmdHis");
            if($ex == '2007') { //导出excel2007文档
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="'.$timestamp.'.xlsx"');
                header('Cache-Control: max-age=0');
                $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
                $objWriter->save('php://output');
                exit;
            } else {  //导出excel2003文档
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="'.$timestamp.'.xls"');
                header('Cache-Control: max-age=0');
                $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, 'Excel5');
                $objWriter->save('php://output');
                exit;
            }
    }

    /**
     *
     * 显示导入页面 ...
     */

    /**实现导入excel
     **/
    function impUser(){
        if (!empty($_FILES)) {
            $upload = new \Think\Upload();// 实例化上传类
            $filepath='./Public/Excle/'; 
            $upload->exts = array('xlsx','xls');// 设置附件上传类型
            $upload->rootPath  =  $filepath; // 设置附件上传根目录
            $upload->saveName  =     'time';
            $upload->autoSub   =     false;
            if (!$info=$upload->upload()) {
                $this->error($upload->getError());
            }
            foreach ($info as $key => $value) {
                unset($info);
                $info[0]=$value;
                $info[0]['savepath']=$filepath;
            }
            vendor("PHPExcel.PHPExcel");
            $file_name=$info[0]['savepath'].$info[0]['savename'];
            $objReader = \PHPExcel_IOFactory::createReader('Excel5');
            $objPHPExcel = $objReader->load($file_name,$encode='utf-8');
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow(); // 取得总行数
            $highestColumn = $sheet->getHighestColumn(); // 取得总列数
            $j=0;
            for($i=3;$i<=$highestRow;$i++)
            {
                $data['name']= $objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
                $tname= $objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
                $data['tid']=Gettid($tname);
                $data['danwei']= $objPHPExcel->getActiveSheet()->getCell("D".$i)->getValue();
                $data['phone']= $objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
                // if(M('Contacts')->where("name='".$data['name']."' and phone=$data['phone']")->find()){
                if(M('Contacts')->where("phone='".$data['phone']."'")->find()){
                    //如果存在相同联系人。判断条件：电话 两项一致，上面注释的代码是用姓名/电话判断
                }else{
                    M('Contacts')->add($data);
                    $j++;
                }
            }
            unlink($file_name);
            User_log('批量导入联系人，数量：'.$j);
            $this->success('导入成功！本次导入联系人数量：'.$j);
        }else
        {
            $this->error("请选择上传的文件");
        }
    }
    
}

?>