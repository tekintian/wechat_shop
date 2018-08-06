# 小程序服务端 server

## 环境搭建

  1) apache

    yum install -y httpd mod_ssl

    vi /etc/selinux/config:
      SELINUX=disabled

    systemctl stop firewalld
    systemctl disable firewalld

    /etc/httpd/conf.d/ssl.conf:
      SSLCertificateFile /etc/pki/tls/certs/41833233.cn/41833233.cn.crt
      SSLCertificateKeyFile /etc/pki/tls/certs/41833233.cn/41833233.cn.key

    /etc/httpd/conf/httpd.conf:
      AllowOverride all

      DirectoryIndex index.html index.php

      Include conf.modules.d/*.conf
      LoadModule rewrite_module modules/mod_rewrite.so

    systemctl start httpd.service
    systemctl enable httpd.service

  2) mariadb

    yum install -y mariadb mariadb-server

    systemctl start mariadb
    systemctl enable mariadb

    mysql_secure_installation

  3) php

    yum install -y php php-mysql

    开启pathinfo模式支持:
    php.ini: cgi.fix_pathinfo=0

## 后台部署

  1) 拷贝server下的文件到/var/www/html目录下

    cd /var/www/html
    mkdir App/Runtime
    chmod -R 0777 App/Runtime

  2) 后台配置
  
