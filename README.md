# GetBingImage

获取必应每日图片，采用PDO方式连接数据库。

DEMO: http://b.pwm.hu/bimg

## 使用

### 未启用伪静态

#### 跳转图片

```
https://youdomain/bing/index.php?param=redirect
```

#### 直接输出图片链接

```
https://youdomain/bing/index.php?param=link
```

#### 输出JSON数据

```
https://youdomain/bing/index.php
```

#### 输出XML数据

```
https://youdomain/bing/index.php?param=xml
```

### 已启用伪静态

#### 跳转图片

```
https://youdomain/bimg/redirect
```

#### 直接输出图片链接

```
https://youdomain/bimg/link
```

#### 输出JSON数据

```
https://youdomain/bimg/
```

#### 输出XML数据

```
https://youdomain/bimg/xml
```



## 伪静态

### Nginx

```
location /
{
	if (-f $request_filename) {
        break;
	}
    rewrite ^/(.*)$ /index.php?param=$1 last;
}

```

### Nginx（子目录）

```
location /bimg/
{
    if (-f $request_filename) {
        break;
    }
    rewrite /bing/(.*)$ /bing/index.php?param=$1 last;
}
```

### Apache（子目录）

请在`index.php`同级建立`.htaccess`

```
RewriteEngine On
RewriteBase /bimg/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?param=$1 [L]
```

## 每日自动获取

使用 Crontab 定时访问`https://youdomain/bing/index.php`即可

## 感谢

https://github.com/KaiDiLiang/getBingImgs

## 授权

随便你™怎么改
