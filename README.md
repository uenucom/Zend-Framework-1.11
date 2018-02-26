Zend Framework 1.11 

=======================

基于Zend Framework 1.11  研发的多模块管理系统，适合大项目，每个模块独立开发、测试、上线，低耦合，不影响其他项目。

非常有创意的项目，试用复杂的大型项目。

可在如下php版本运行
支持php 5.x
（5.2、5.3、5.4、5.5、5.6）

不支持php7.0+

### 模块说明
实例中的default模块包含如下功能，可以参考此模块扩展开发其他模块如（Book）
1、用户管理
2、权限管理
3、批量授权（新增权限、移除权限，根据某人开通权限）
4、会话管理（可以查看当前在线用户，可踢掉指定用户），一个用户重复登录，仅保留最近一次会话，之前会话自动失效。
5、日志管理 （用户权限的变更、操作日志等）
6、导出数据


Using Git submodules
--------------------
Alternatively, you can install using native git submodules:

    git clone https://github.com/tianhuimin/Zend-Framework-1.11.git



Web Server Setup
----------------

### PHP CLI Server

The simplest way to get started if you are using PHP 5.4 or above is to start the internal PHP cli-server in the root directory:

    php -S 0.0.0.0:8080 -t public/ public/index.php

This will start the cli-server on port 8080, and bind it to all network
interfaces.

**Note: ** The built-in CLI server is *for development only*.

### Apache Setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:

    <VirtualHost *:80>
        ServerName zf2-tutorial.localhost
        DocumentRoot /path/to/zf2-tutorial/public
        SetEnv APPLICATION_ENV "development"
        <Directory /path/to/zf2-tutorial/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    </VirtualHost>

### Nginx Setup

```nginx

server {
  listen 80;
  server_name  zfapp.localhost manage.uenu.com;
  root /home/work/www/manage/public;
  location / {
        index index.php index.html index.htm;
        try_files $uri $uri/ /index.php?$args;
  }
  access_log  /home/work/logs/default_access.log  main;
  error_log  /home/work/logs/default_error.log  crit;
  

  location ~ \.php$ {
            fastcgi_pass   127.0.0.1:9000;
            #fastcgi_pass   unix:/dev/shm/php-cgi7.sock;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME   $document_root$fastcgi_script_name;
            include        fastcgi_params;
            client_max_body_size 10m;
        }
}
```

Restart the nginx, now you should be ready to go!


### 技术支持
Email:uenucom#163.com
 ( change # to @)

### Download
http://github.uenu.com
