<?php

//测试函数
require_once('print.php');

/**
 * @Author: Tekin
 * @Date:   2018-04-22 15:49:45
 * @Last Modified 2018-04-22
 */
if ( ! function_exists('checkorderstatus')) {

    /**
     * 获取和设置配置参数 支持批量定义
     * 在线交易订单支付处理函数
     * 函数功能：根据支付接口传回的数据判断该订单是否已经支付成功；
     * 返回值：如果订单已经成功支付，返回true，否则返回false；
     *
     * @param      <type>   $ordid      The ordid
     * @param      <type>   $parameter  The parameter
     *
     * @return     boolean  ( description_of_the_return_value )
     */
     function checkorderstatus($ordid,$parameter){
         $row=M('order')->field('price,status')->where('ordernum='.$ordid)->select();
         file_put_contents("w.txt",$row[0]['price']);
         file_put_contents("ww.txt",$row[0]['status']);
         if($parameter==$row[0]['price']){//实际支付与订单价格相等
                //并且订单未支付 
                return true;
         }else{
            return false;   
         }      
        }
}

if ( ! function_exists('orderhandle')) {
     /**
      * 处理订单函数
      * 更新订单状态，写入订单支付后返回的数据
      * 
      * @param      <type>  $parameter  The parameter
      *
      * @return     <type>  ( description_of_the_return_value )
      */
     function orderhandle($parameter){
        $ordid=$parameter['out_trade_no'];
        $data['payment_trade_no']      =$parameter['trade_no'];
        $data['payment_trade_status']  =$parameter['trade_status'];
        $data['payment_notify_id']     =$parameter['notify_id'];
        $data['payment_notify_time']   =$parameter['notify_time'];
        $data['payment_buyer_email']   =$parameter['buyer_email'];
        $data['ordstatus']             =1;
        $datas['status']=1;
        $datas['price_h']=$parameter['total_fee'];
        /*******解决屠涂同一订单重复支付问题 lisa**********/
        if(strlen($ordid)==16){//屠涂修改订单号唯一
            $ordstatus=M('order')->where('order_sn='.$ordid)->save($datas);
        }else{
            $ordstatus=M('order')->where('id='.$ordid)->save($datas);
        }
     }
}

if ( ! function_exists('i_array_column')) {
    function i_array_column($input, $columnKey, $indexKey=null){
        if(!function_exists('array_column')){ 
            $columnKeyIsNumber  = (is_numeric($columnKey))?true:false; 
            $indexKeyIsNull            = (is_null($indexKey))?true :false; 
            $indexKeyIsNumber     = (is_numeric($indexKey))?true:false; 
            $result                         = array(); 
            foreach((array)$input as $key=>$row){ 
                if($columnKeyIsNumber){ 
                    $tmp= array_slice($row, $columnKey, 1); 
                    $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
                }else{ 
                    $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
                } 
                if(!$indexKeyIsNull){ 
                    if($indexKeyIsNumber){ 
                      $key = array_slice($row, $indexKey, 1); 
                      $key = (is_array($key) && !empty($key))?current($key):null; 
                      $key = is_null($key)?0:$key; 
                    }else{ 
                      $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                    } 
                } 
                $result[$key] = $tmp; 
            } 
            return $result; 
        }else{
            return array_column($input, $columnKey, $indexKey);
        }
     }
}

if ( ! function_exists('pw_tmp_en')) {
    /**
     * tmp file
     * @author     (tekin <tekintian@domain.com>)
     * @param      <type>  $str    The string
     * @return     <type>  ( description_of_the_return_value )
     */
    function pw_tmp_en($str){
       return  md5(hash('sha512',$str).'85EE6F8671A342248E');
    }
}




