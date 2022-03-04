<?php
$dir = dirname(__FILE__);
define("__BASE_PATH__", $dir);

// 载入核心类
$bingPath = $dir . DIRECTORY_SEPARATOR . 'bing.php';
if (!is_file($bingPath)) die("核心文件缺失!");
require_once $bingPath;
$bing = new Bing;
$info = $bing->getLatestImageInfo();
if (array_key_exists('param', $_GET) && $_GET['param']) {
    switch ($_GET['param']) {
        case 'redirect':
            header('Location: ' . $info['url'], true, 302);
            break;
        case 'link':
            header("Content-type: text/plain");
            echo $info['url'];
            break;
        case 'json':
            unset($info['raw']);
            printJson($info);
            break;
        default:
            break;
    }
} else {
    ?>
    <html>
    <meta charset="utf-8">
    <meta name="description" content="必应每日图片">
    <meta name="keywords" content="必应, 每日图片, Bing, picture, everyday">
    <meta name="author" content="Ryan Lieu">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "必应每日图片"; ?></title>
    <body>
    <div></div>
    <div class="wrap">
        <div class="title">欢迎使用<a href="https://github.com/benzbrake/GetBingImage">GetBingImage</a></div>
        <div class="json"><a href="json">JSON</a></div>
        <div class="link"><a href="link">LINK</a></div>
        <div class="redirect"><a href="redirect">WATCH</a></div>
    </div>
    <style>
        a {
            color: indianred;
            text-decoration: none
        }

        .wrap {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .title {
            font-size: 2em
        }

        @media (min-width: 64em) {
            .title {
                font-size: 4em
            }
        }


    </style>
    </body>
    </html>
    <?php
}
