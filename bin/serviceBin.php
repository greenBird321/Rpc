<?php

/**
 * 数据缓存脚本
 * Redis[3]
 */
namespace Xt\Bin;


use Symfony\Component\Yaml\Yaml;
use DateTime;
use DateTimeZone;
use PDO;
include __DIR__ . '/../vendor/autoload.php';

$tempconfig = Yaml::parse(file_get_contents(__DIR__ . "/../app/Config/app.yml"));
$timezone = $tempconfig['setting']['timezone'];
ini_set("date.timezone", $timezone);
ini_set('memory_limit', -1);
ini_set('max_execution_time', '0');

class Service
{

    private $_RUN_TIME_START;
    private $_RUN_TIME_END;

    private $_redis;            // redis实例
    private $config;            // app.yml
    private $_game;             // 1001008
    private $_app;              // HT_app
    private $_lang;             // zh_CN
    private $_date;             // 20170601
    private $_month;


    public function __construct()
    {
        $this->_RUN_TIME_START = time();
    }


    public function __destruct()
    {
    }


    /**
     * TODO :: 定期清理过期缓存
     */
    public function run()
    {
        $this->getOptions();

        $this->init();

        $this->setSpend();

        $this->takeUp();
    }


    /**
     * 缓存到Redis
     * 格式 游戏ID:act:活动ID   例 1001008:act:123
     * @return bool
     */
    public function setSpend()
    {

        $this->logger('SET SPEND START');

        // app config
        $app_config = $this->readAppConfig();
        $app_config['setting']['timezone'];


        // 服务器列表
        $zone_list = $this->getZoneList();


        // 获取当前活动
        $dateTime = date('Y-m-d H:i:s');
        $sql = "SELECT id,type,start_time,end_time FROM activity WHERE status=1 AND ('$dateTime' BETWEEN start_time AND end_time)";
        $query = $this->db('service')->query($sql);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $act_list = $query->fetchAll();
        if (!$act_list) {
            return false;
        }


        // 循环活动
        foreach ($act_list as $act) {

            // 仅处理消费活动
            if ($act['type'] != 'spend') {
                continue;
            }

            // 时区转换 (转为游戏时区时间)
            $start = $this->switchTimeZone($app_config['setting']['timezone'], $this->config['setting']['timezone'], $act['start_time']);
            $end = $this->switchTimeZone($app_config['setting']['timezone'], $this->config['setting']['timezone'], $act['end_time']);

            $redis_key = "{$this->_game}:act:{$act['id']}";

            // 循环服务器
            foreach ($zone_list as $zone => $cfg) {
                $this->logger('ZONE: ' . $zone);
                $sql = "SELECT role_id user_id, SUM(ABS(gold)) coin FROM `gold_log` WHERE gold<0 AND log_time>='{$start}' AND log_time<='{$end}' GROUP BY role_id";
                $query = $this->db($zone)->query($sql);
                $query->setFetchMode(PDO::FETCH_ASSOC);
                $users_list = $query->fetchAll();
                if (!$users_list) {
                    continue;
                }

                // 存入redis
                $this->logger('TO REDIS: ' . $zone);
                foreach ($users_list as $user) {
                    $this->redis()->zadd($redis_key, $user['coin'], $zone . '-' . $user['user_id']);
                }
            }
        }
        $this->logger('SET SPEND END');
    }


    /**
     * 初始化
     */
    private function init()
    {
        $this->logger('INIT CONFIG');
        $this->readConfig();
    }


    /**
     * 切换时区
     * @param string $timeZone
     * @param string $fromTimeZone
     * @param string $datetime
     * @param string $format
     * @return string
     */
    private function switchTimeZone($timeZone = '', $fromTimeZone = 'UTC', $datetime = '', $format = 'Y-m-d H:i:s')
    {
        $date = new DateTime($datetime, new DateTimeZone($fromTimeZone));
        $date->setTimezone(new DateTimeZone($timeZone));
        return $date->format($format);
    }


    /**
     * 服务器列表
     * @return array
     */
    private function getZoneList()
    {
        $config = $this->readAppConfig();

        $result = [];
        foreach ($config as $key => $value) {
            if (intval($key) <= 0) { // 过滤非数字开头配置
                continue;
            }
            $result[$key] = $value;
        }
        return $result;
    }


    /**
     * redis连接
     * @return Redis
     */
    private function redis()
    {
        if (empty($this->_redis)) {
            $redis = new \Redis();
            $redis->connect($this->config['redis']['host'], $this->config['redis']['port']);
            $redis->select(3);
            $this->_redis = $redis;
        }
        return $this->_redis;
    }


    /**
     * redis删除数据
     * @param $key
     */
    private function redisDel($key)
    {
        $keys = $this->redis()->keys($key);
        if ($keys) {
            foreach ($keys as $k) {
                $this->redis()->del($k);
            }
        }
    }


    /**
     * 读取配置
     */
    private function readConfig()
    {
        $this->config = Yaml::parse(file_get_contents(__DIR__ . "/../app/Config/app.yml"));
    }


    /**
     * 读取配置
     */
    private function readAppConfig()
    {
        return Yaml::parse(file_get_contents(__DIR__ . "/../app/Config/{$this->_app}/{$this->_lang}.yml"));
    }


    /**
     * 连接数据库
     * @param string $handle
     * @return PDO
     */
    private function db($handle = '')
    {
        switch ($handle) {
            case 'service':
                $config = Yaml::parse(file_get_contents(__DIR__ . "/../app/Config/app.yml"));
                $params = $config['db_data'];
                break;
            default:
                $config = $this->readAppConfig();
                $params = $config[$handle];
        }

        $dsn = 'mysql:host=' . $params['host'] . ';port=' . $params['port'] . ';dbname=' . $params['db'];
        $db = new PDO($dsn, $params['user'], $params['pass']);
        $db->query('set names ' . $params['charset']);
        return $db;
    }


    /**
     * 日志
     * @param string $msg
     */
    private function logger($msg = '')
    {
        print date('Y-m-d H:i:s O ') . $msg . "\r\n";
    }


    /**
     * 执行信息
     */
    private function takeUp()
    {
        $this->_RUN_TIME_END = time();
        $this->logger('---------------');
        $this->logger('占用内存: ' . round(memory_get_usage() / 1024 / 1024, 2) . 'M');
        $this->logger('执行时间: ' . round(($this->_RUN_TIME_END - $this->_RUN_TIME_START) / 60, 2) . '分钟');
        $this->logger('-----------------------------------------');
    }


    /**
     * 设置参数
     */
    private function getOptions()
    {
        $options = getopt('g:a:l:ih', ['gid:', 'app:', 'lang:']);

        if (isset($options['h'])) {
            $help = <<<END
-------------------------------------------
使用:
php serviceBin.php -g 1001008 -a HT_app -l zh_CN
-------------------------------------------
-h              帮助
-i              显示配置信息
-g  --gid       游戏ID 1001001
-a  --app       配置目录 XT_app
-l  --lang      配置目录 zh_CN
-------------------------------------------\r\n
END;
            print_r($help);
            exit;
        }

        if (isset($options['g'])) {
            $this->_game = $options['g'];
        }
        else {
            print_r('error: unset --gid' . "\r\n");
            exit;
        }

        if (isset($options['a'])) {
            $this->_app = $options['a'];
        }
        else {
            print_r('error: unset --app' . "\r\n");
            exit;
        }

        if (isset($options['l'])) {
            $this->_lang = $options['l'];
        }
        else {
            print_r('error: unset --lang' . "\r\n");
            exit;
        }
    }

}


$audit = new Service();
$audit->run();