<?php

class WxConfig {
  const APPID           = 'wx5afa288e60d81f0e';               // 微信appid
  const SECRET          = '34d14ff3408722e42954aae293ef40a9'; // 微信secret
  const NOTIFY_URL      = 'https://scwhbs.com/index.php/Api/Wxpay/notify';

  const MCHID           = '1511828741';
  const KEY             = 'sxylhaobangshoukejisongcaiwa2018';
  const WXPAY_HTTPS     = 'https://api.mch.weixin.qq.com';

  // sandboxnew

  /*
    <xml>
      <appid>wx5afa288e60d81f0e</appid>
      <body>test</body>
      <device_info>1000</device_info>
      <mch_id>1511828741</mch_id>
      <nonce_str>sxylhaobangshoukejisongcaiwa2018</nonce_str>
      <sign>36CEE78BD73034AD0657F33F77EE0743</sign>
    </xml>

    <sandbox_signkey><![CDATA[cc972accea83fa5ffc67d3956970203b]]></sandbox_signkey>
  */

  // const KEY             = 'sxylhaobangshoukejisongcaiwa2018';
  // const WXPAY_HTTPS     = 'https://api.mch.weixin.qq.com/sandboxnew';
  // const SANDBOX_SIGNKEY = 'cc972accea83fa5ffc67d3956970203b';
}

class DBConfig {
  const DB_USER         = 'root';                             // 用户名
  const DB_PWD          = '123456';                           // 数据库用户密码
}

?>