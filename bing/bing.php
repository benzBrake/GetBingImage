<?php
require_once "util.php";

class Bing
{
    private $saveToDatabase = false;
    private $dbHost = "127.0.0.1";
    private $dbPort = 3306;
    private $dbUser = "root";
    private $dbPass = "root";
    private $dbName;
    private $db;
    private $tableName = 'bingdata'; // 表名

    public function __construct()
    {
        // 读取配置
        $configPath = "config.inc.php";
        if (is_file($configPath)) {
            require_once $configPath;
            if (defined("__SAVE_TO_DATABASE__")) $this->saveToDatabase = boolval(__SAVE_TO_DATABASE__);
            if (defined("__DATABASE_NAME__")) $this->dbName = __DATABASE_NAME__;
            if (defined("__DATABASE_HOST__")) $this->dbHost = __DATABASE_HOST__;
            if (defined("__DATABASE_HOST__")) $this->dbPort = __DATABASE_PORT__;
            if (defined("__DATABASE_USER__")) $this->dbUser = __DATABASE_USER__;
            if (defined("__DATABASE_PASS__")) $this->dbPass = __DATABASE_PASS__;
            if (defined("__TABLE_NAME__")) $this->tableName = __TABLE_NAME__;
        }

        $dsn = "mysql:host={$this->dbHost};port={$this->dbPort};dbname={$this->dbName}";
        $driver_options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',);

        try {
            $this->db = new PDO($dsn, $this->dbUser, $this->dbPass, $driver_options);
            $this->initDb($this->dbName);
        } catch (Exception $exception) {
            printJson(['status' => 'error', 'code' => $exception->getCode(), 'message' => $exception->getMessage()]);
        }
    }

    /**
     * 数据库初始化
     * @param $dbName 数据库名
     * @return void
     */
    public function initDb()
    {
        if (defined("__INIT_DATABASE__") && __INIT_DATABASE__) {
            $sql = "CREATE TABLE IF NOT EXISTS {$this->tableName}(
            `id` int(8) NOT NULL AUTO_INCREMENT,
            `url` varchar(512) NOT NULL COMMENT '图片链接',
            `stamp` int(11) NULL DEFAULT NULL COMMENT '图片时间',
            `copyright` varchar(6400) NOT NULL COMMENT '图片版权', 
            `copyrightLink` varchar(512) NOT NULL COMMENT '版权链接',
            PRIMARY KEY (`id`) USING BTREE) DEFAULT CHARSET=UTF8mb4 AUTO_INCREMENT=1";
            $this->db->exec($sql);
        }
    }

    /**
     * 获取 XML 数据
     * @return false|string
     */
    public function getLatestXML()
    {
        return file_get_contents('https://cn.bing.com/HPImageArchive.aspx?idx=0&n=1');
    }

    /**
     * 获取当前时间戳
     * @return int
     */
    public function getStamp()
    {
        return strtotime(date('Ymd', time()));
    }

    /**
     * 从数据库获取当天数据
     * @return array
     */
    public function getLatestFromDb()
    {
        $stamp = $this->getStamp();
        $sql = "select * from `{$this->tableName}` where `stamp` = '{$stamp}'";
        $info = $this->db->query($sql);
        $result = $info->fetchAll(PDO::FETCH_ASSOC);
        if (count($result)) return $result[0];
        return [];
    }

    /**
     * 获取每日图片信息
     * @return array
     */
    public function getLatestImageInfo()
    {
        $result = $this->getLatestFromDb();
        if (count($result)) {
            $imgInfo = $result;
            $imgInfo['date'] = date('Y-m-d', $imgInfo['stamp']);
            unset($imgInfo['id']);
            unset($imgInfo['stamp']);
            unset($imgInfo['count']);
        } else {
            $xmlStr = $this->getLatestXML();
            $XmlToJsonClass = new XmlToJson;
            $arr = $XmlToJsonClass->Parse($xmlStr);
            $imgUrl = "https://cn.bing.com" . $arr['image']['url'];
            $imgTime = strtotime($arr['image']['startdate']);
            $imgCopyright = $arr['image']['copyright'];
            $copyrightLink = $arr['image']['copyrightlink'];
            if ($imgUrl && $this->saveToDatabase) {
                $sql = "select * from `{$this->tableName}` where `url` = '{$imgUrl}'";
                $info = $this->db->query($sql);
                $result = $info->fetchAll(PDO::FETCH_ASSOC);
                if (!count($result)) {
                    $sql = "insert into `{$this->tableName}` (`url`, `stamp`, `copyright`, `copyrightLink`)values('{$imgUrl}', '{$imgTime}', '{$imgCopyright}', '{$copyrightLink}')";
                    $this->db->exec($sql);
                }
            }
            $imgInfo = ['url' => $imgUrl, 'date' => date('Y-m-d', $imgTime), 'copyright' => $imgCopyright, 'copyrightLink' => $copyrightLink];
        }
        return $imgInfo;
    }
}
