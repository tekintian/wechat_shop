<?php
//各目录公用funcion,如要改要考虑各目录

//二维数组排序
//是否手机访问
//是否微信访问
//创建文件夹
//取IP地址
//分页

//php 去除html标签 js 和 css样式 - 最爱用的一个PHP清楚html格式函数
function clearhtml($content) {  
   $content = preg_replace("/<a[^>]*>/i", "", $content);  
   $content = preg_replace("/<\/a>/i", "", $content);   
   $content = preg_replace("/<div[^>]*>/i", "", $content);  
   $content = preg_replace("/<\/div>/i", "", $content);      
   $content = preg_replace("/<!--[^>]*-->/i", "", $content);//注释内容
  // $content = preg_replace("/style=.+?['|\"]/i",'',$content);//去除样式  
   $content = preg_replace("/class=.+?['|\"]/i",'',$content);//去除样式  
   $content = preg_replace("/id=.+?['|\"]/i",'',$content);//去除样式     
   $content = preg_replace("/lang=.+?['|\"]/i",'',$content);//去除样式      
   $content = preg_replace("/width=.+?['|\"]/i",'',$content);//去除样式  
   
   $content = preg_replace("/width:.+?['|\;]/i",'',$content);//去除样式
   $content = preg_replace("/height:.+?['|\;]/i",'',$content);//去除样式
    
   $content = preg_replace("/height=.+?['|\"]/i",'',$content);//去除样式   
   $content = preg_replace("/border=.+?['|\"]/i",'',$content);//去除样式   
   $content = preg_replace("/face=.+?['|\"]/i",'',$content);//去除样式   
   $content = preg_replace("/face=.+?['|\"]/",'',$content);//去除样式只允许小写正则匹配没有带 i 参数
   return $content;
}

function get_http_array($url,$post_data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   //没有这个会自动输出，不用print_r();也会在后面多个1
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$output = curl_exec($ch);
		curl_close($ch);
		$out = json_decode($output);
		$data = object_array($out);
		return $data;
}


function object_array($array)
{
   if(is_object($array))
   {
    $array = (array)$array;
   }
   if(is_array($array))
   {
    foreach($array as $key=>$value)
    {
     $array[$key] = object_array($value);
    }
   }
   return $array;
}

function get_config(){
	$config_org = S('config_org');	
	if(!$config_org){
		$lyb_config = M('lyb_config');
		$config_org = $lyb_config->field('code,value')->select();
		S('config_org',$config_org);
	}
	
	$config_list = array();
	foreach($config_org as $k => $v){
		$config_list[$v['code']] = $v['value'];
	}
	return $config_list;
}

//是否手机访问
function isMobile(){  
	$useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';  
	$useragent_commentsblock=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';  	  
	function CheckSubstrs($substrs,$text){  
		foreach($substrs as $substr)  
			if(false!==strpos($text,$substr)){  
				return true;  
			}  
			return false;  
	}
	$mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
	$mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');  
		  
	$found_mobile=CheckSubstrs($mobile_os_list,$useragent_commentsblock) ||  
			  CheckSubstrs($mobile_token_list,$useragent);  
		  
	if ($found_mobile){  
		return true;  
	}else{  
		return false;  
	}  
}

function get_org_url($php,$m='',$c='',$a='',$list=array()){
	if(!$m || !$a || !$a){
		$url = SELF_ROOT.$php;
	}else{
		$url = SELF_ROOT.$php.'?m='.$m.'&c='.$c.'&a='.$a;
	}
	if($list){
		$list_str = '';
		foreach($list as $k=>$v){
			$list_str = $list_str.'&';
			$list_str = $list_str.$k.'='.$v;	
		}
		$url = $url.$list_str;
	}
	return $url;
}

function get_region_name($region_id){
	$region_name = M('all_region')->getFieldByregion_id($region_id,'region_name');
	return $region_name;	
}

function browse_insert($cat,$id=0){
	$ip = getIP();
	M('lyb_browse')->add(array('item'=>$id,'cat'=>$cat,'add_time'=>time(),'ip'=>$ip));
}

//是否微信访问
function is_weixin()
{ 
    if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
        return true;
    }  
        return false;
}

//取IP地址
function getIP() { 
if (getenv('HTTP_CLIENT_IP')) { 
$ip = getenv('HTTP_CLIENT_IP'); 
} 
elseif (getenv('HTTP_X_FORWARDED_FOR')) { 
$ip = getenv('HTTP_X_FORWARDED_FOR'); 
} 
elseif (getenv('HTTP_X_FORWARDED')) { 
$ip = getenv('HTTP_X_FORWARDED'); 
} 
elseif (getenv('HTTP_FORWARDED_FOR')) { 
$ip = getenv('HTTP_FORWARDED_FOR'); 

} 
elseif (getenv('HTTP_FORWARDED')) { 
$ip = getenv('HTTP_FORWARDED'); 
} 
else { 
$ip = $_SERVER['REMOTE_ADDR']; 
}

if(strstr($ip,',')){
$ip = substr($ip,0,strrpos($ip,','));  
}
return $ip; 
} 

//创建文件夹
 function createFolder($path)
 {
  if (!file_exists($path))
  {
   createFolder(dirname($path));
   mkdir($path, 0777);
  }
 }


//分页1：总数，2每页显示，3当前页，4显示页数，5，偏移值
function page_fu($count,$size,$page,$_pagenum = 10,$_offset = 2){
	$page_count = ($count > 0) ? intval(ceil($count / $size)) : 1;
	$pager['page']         = $page;
    $pager['size']         = $size;
    $pager['record_count'] = $count;
    $pager['page_count']   = $page_count;
    $pager['page_first']   = "1";
    $pager['page_prev']    = ($page > 1) ? $page - 1 : 1;
    $pager['page_next']    = ($page < $page_count) ? $page + 1 : $page_count;
    $pager['page_last']    = $page < $page_count ? $page_count : $page;
    $_from = $_to = 0;  // 开始页, 结束页
    if($_pagenum > $page_count)	//如果显示页码10 多于现有页码，最后一面就是现有页码
    {
        $_from = 1;
        $_to = $page_count;
    }
    else
    {
            $_from = $page - $_offset;//如果显示页码10 少于现有页码，
            $_to = $_from + $_pagenum - 1;
            if($_from < 1)
    		{
      			$_to = $page + 1 - $_from;
                $_from = 1;
                if($_to - $_from < $_pagenum)
                {
                    $_to = $_pagenum;
                }
            }
            elseif($_to > $page_count)
            {
                $_from = $page_count - $_pagenum + 1;
                $_to = $page_count;
            }
    }
        $pager['page_number'] = array();
        for ($i=$_from;$i<=$_to;++$i)
        {
            $pager['page_number'][$i] =  $i;
        }	
	return $pager;
}
//二维数组排序
function array_sort($arr,$keys,$type='asc'){ 
	$keysvalue = $new_array = array();
	foreach ($arr as $k=>$v){
		$keysvalue[$k] = $v[$keys];
	}
	if($type == 'asc'){
		asort($keysvalue);
	}else{
		arsort($keysvalue);
	}
	reset($keysvalue);
	foreach ($keysvalue as $k=>$v){
		$new_array[$k] = $arr[$k];
	}
	return $new_array; 
}