# 小程序服务端 server_api

## 小程序服务端 server_api 部署方法

- PS: 注意，你的服务器一定要支持url rewrite模式，且PHP版本最好是 PHP5.6 ， 支持PHP7.0;

### 部署步骤 

1. 上传 server_api 目录下的所有代码到你的服务器并运行   yourname.com/adminer.php 使用你的mysql账号登录后 导入数据 wechat_shop.sql.gz 【会自动创建数据库和导入演示数据】

2. 修改App/Common/Conf/db.php 里面的数据库连接参数为你自己的；

 - 只需要修改你的数据库用户名和密码即可
```conf
'DB_USER'               =>  'root',      // 用户名
'DB_PWD'                =>  '123456',      //数据库用户密码
```

3. App/Api/Conf/config.php 微信小程序的appid、secret、mchid、key、notify_url，SELF_ROOT的参数修改；

4. ThinkPHP\Library\Vendor\wxpay\lib\WxPay.Config.php  微信小程序的appid、appsecret、mchid、key参数修改；

5. ThinkPHP\Library\Vendor\WeiXinpay\lib\WxPay.Config.php  微信小程序的appid、appsecret、mchid、key、notify_url参数修改；

6. App/Api/Controller/WxPayController.class.php 50行修改链接

7. 必须开启 URL Rewrite才能使用本系统

- 后台登录地址： youname.com/Admin/Login/index.html  用户名是admin，密码是123456


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

## Apache URL Rewrite配置示例
.htaccess 文件
```htaccess
<IfModule mod_rewrite.c>
  Options +FollowSymlinks
  RewriteEngine On

  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteRule ^(.*)$  /index.php?s=$1 [QSA,PT,L]
</IfModule>
```