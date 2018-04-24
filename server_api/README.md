# 小程序服务端 server_api

使用数据库管理工具 adminer 导入数据 wechat_shop.sql

    1、更名App/Common/Conf/db.sample.php 为db.php 并修改数据库连接参数为你自己创建的；

    2、App/Api/Conf/config.php 微信小程序的appid、secret、mchid、key、notify_url，SELF_ROOT的参数修改；

    3、ThinkPHP\Library\Vendor\wxpay\lib\WxPay.Config.php  微信小程序的appid、appsecret、mchid、key参数修改；

    4、ThinkPHP\Library\Vendor\WeiXinpay\lib\WxPay.Config.php  微信小程序的appid、appsecret、mchid、key、notify_url参数修改；

    5、App/Api/Controller/WxPayController.class.php 50行修改链接


    后台登录地址： youname.com/index.php/admin  用户名是admin，密码是123456
