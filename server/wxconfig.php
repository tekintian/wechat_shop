<?php

class WxConfig {
  const APPID           = 'wx5afa288e60d81f0e';               // 微信appid
  const SECRET          = '34d14ff3408722e42954aae293ef40a9'; // 微信secret

  const MCHID           = '101198986';
  const KEY             = 'sxylhaobangshoukejisongcaiwa2018';
  const NOTIFY_URL      = 'https://scwhbs.com/index.php/Api/Wxpay/notify';

  // const WXPAY_HTTPS  = 'https://api.mch.weixin.qq.com';
  const WXPAY_HTTPS     = 'https://api.mch.weixin.qq.com/sandboxnew';
}

class DBConfig {
  const DB_USER         = 'root';                             // 用户名
  const DB_PWD          = '123456';                           // 数据库用户密码
}

?>