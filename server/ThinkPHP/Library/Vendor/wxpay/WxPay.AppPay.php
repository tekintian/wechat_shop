<?php
  
/**
* 
* APP支付实现类
* @author widyhu
*
*/
class AppPay
{    
    /**
     * 
     * 参数数组转换为url参数
     * @param array $urlObj
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            $buff .= $k . "=" . $v . "&";
        }
         
        $buff = trim($buff, "&");
        return $buff;
    }
     
    /**
     * 
     * 生成直接支付url，支付url有效期为2小时,模式二
     * @param UnifiedOrderInput $input
     */
    public function GetPayPrepayId($input)
    {
        if($input->GetTrade_type()=="APP")
        {
            $result = WxPayApi::unifiedOrder($input);
            return $result;
        }
    }
    /*生成APP提交数据*/
    public function GetAppApiParameters($UnifiedOrderResult)
    {
        if(!array_key_exists("appid", $UnifiedOrderResult)
        || !array_key_exists("prepay_id", $UnifiedOrderResult)
        || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new WxPayException("参数错误");
        }
        $appapi = new WxPayAppApiPay();
        $appapi->SetAppid($UnifiedOrderResult["appid"]);
        $appapi->SetPartnerId($UnifiedOrderResult["mch_id"]);
        $appapi->SetPrepayId($UnifiedOrderResult["prepay_id"]);
        $timeStamp = time();
        $appapi->SetTimeStamp($timeStamp);
        $appapi->SetNonceStr(WxPayApi::getNonceStr());
        $appapi->SetPackage("Sign=WXPay");
        $appapi->SetSign($appapi->MakeSign());
        $back_arr=$appapi->GetValues();
        $back_arr['prepay_id']=$UnifiedOrderResult["prepay_id"];
        $parameters = json_encode($appapi->GetValues());
        return $parameters;
    }
}