# `Tinyphonference` 部署

## 环境要求
- php 7.0 
- nodejs 6.10.3 
- mysql 5.5
- nginx 
- composer 1.4.2 (最近版本即可)

## php配置
开启扩展 去掉";"
```angular2html
extension=php_fileinfo.dll
extension=php_openssl.dll

```


## 安装
第一步
```apacheconfig
composer install
```
第二步
```apacheconfig
npm install
```
第三步安装数据库脚本
```apacheconfig
php artisan migrate
```
第四步执行自动加载自定义类
```apacheconfig
composer dump-autoload
```

## 在linux下部署
第一步添加nginx文件
```nginx
server {
    listen       8090;
    server_name  localhost;

    set $root_path '/opt/www/Tinyphonference/public';
    root $root_path;

    index index.php index.html index.htm;
    try_files $uri $uri/ @rewrite;

    location @rewrite {
        rewrite ^/(.*)$ /index.php?_url=/$1;
    }

    location ~ \.php {
        try_files $uri $uri/ /index.php?$query_string;
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  /index.php;
        fastcgi_param  SCRIPT_FILENAME  $root_path$fastcgi_script_name;
        include        fastcgi_params;
    }

    error_page  404              /404.html;
    # redirect server error pages to the static page /50x.html
    error_page   500 502 503 504  /50x.html;
    location = /50x.html {
        root   html;
    }
}

```

第二步设置目录权限
```apacheconfig
chmod -R 777 storage
chmod -R 777 bootstrap/cache

```

第三步添加项目配置文件
```
vim .env
// 复制相应.env config
```
第四步创建数据库
```
tinyphonference.sql 导入这个文件
```

第五步安装数据库脚本  或者 直接导入SQL脚本。
```apacheconfig
php artisan migrate
```

第六步composer/ npm安装第三方库
```
composer install

npm install
```

第七步访问看部署是否成功
```
1 .使用Linux 配置的端口或域名访问
------------------------------------
2. 执行命令访问

php artisan server
-----------------------------------
```



## 以下是生成假数据

直接返回URL 
```
http://localhost:8000/create?password=liushuixingyun&year=2017

或

http://{域名}/create?password=liushuixingyun&year=2017

线上的时候需要屏蔽这个链接。到时候我发代码的时候，我来屏蔽好了。

```




