# GetBingImage

获取必应每日图片，采用PDO方式连接数据库。

DEMO1: http://b.pwm.hu/bimg
DEMO2: https://nongfu.alwaysdata.net/bing

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
https://youdomain/bing/index.php?param=json
```

### 已启用伪静态

#### 跳转图片

```
https://youdomain/bing/redirect
```

#### 直接输出图片链接

```
https://youdomain/bing/link
```

#### 输出JSON数据

```
https://youdomain/bing/json
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
location /bing/
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
RewriteBase /bing/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?param=$1 [L]
```

## 每日自动获取

使用 Crontab 定时访问`https://youdomain/bing/index.php?param=json`即可

## 感谢

https://github.com/KaiDiLiang/getBingImgs

## 授权

随便你™怎么改
