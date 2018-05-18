# 小程序服务端 server_api

## 小程序服务端 server_api 部署方法

	使用数据库管理工具 adminer 导入数据 wechat_shop.sql

	PS: 注意，你的PHP一定要开启path_info模式和url rewrite的支持;

### 部署步骤 

1. 修改App/Common/Conf/db.php 里面的数据库连接参数为你自己的；

 - 只需要修改下面几项就可以

```conf
     'DB_NAME'               =>  'wechat_shop',          // 数据库名
    'DB_PORT'               =>  '3357',        // 端口  
    'DB_HOST'               =>  '172.17.0.1', // 服务器地址
    'DB_USER'               =>  'wechat_shop',      // 用户名
    'DB_PWD'                =>  '888888',      //'123456',          // 密码
```

2. App/Api/Conf/config.php 微信小程序的appid、secret、mchid、key、notify_url，SELF_ROOT的参数修改；

3. ThinkPHP\Library\Vendor\wxpay\lib\WxPay.Config.php  微信小程序的appid、appsecret、mchid、key参数修改；

4. ThinkPHP\Library\Vendor\WeiXinpay\lib\WxPay.Config.php  微信小程序的appid、appsecret、mchid、key、notify_url参数修改；

5. App/Api/Controller/WxPayController.class.php 50行修改链接


- 后台登录地址： youname.com/index.php/admin  用户名是admin，密码是123456


## PHP开启pathinfo模式支持方法
打开你的php.ini配置文件，找到 cgi.fix_pathinfo 将前面的 ; 注释去掉, 后面的值改为0

	cgi.fix_pathinfo=0

## Tengine/nginx配置文件示例
```conf
server {
    listen    80;
    #你的域名，根据实际情况修改
    server_name  wxshop.yunnan.ws;
    #你的server_api文件夹位置，，根据实际情况修改
    root    /home/wechat_shop/server_api;
    #模式页面配置
    index index.html index.php index.htm;
  #这个是你的php的执行配置，根据实际情况修改
  location ~ [^/]\.php(/|$) {
    #fastcgi_pass remote_php_ip:9000;
    fastcgi_pass unix:/dev/shm/php-cgi.sock;
    fastcgi_index index.php;
    include fastcgi.conf;
    }
  #rewrite配置，很重要，如果你不知道你在做什么，请勿修改！！！
  if (!-e $request_filename) {
    rewrite "^/(.*)"  /index.php?s=/$1 last;
    break;
  }
  #禁止访问.ht文件配置
  location ~ /\.ht {
    deny all;
  }
  #禁止上传/静态目录的脚本权限
  location ~* .*\/(Data|public|static|uploads|images)\/.*\.(php|php5|phps|asp|aspx|jsp)$ {
     deny all;
  }
  # access_log
  access_log  /home/wwwlogs/wxshop.dd_access.log  combined;
}
```