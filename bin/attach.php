<?php
/**
 * 群发道具脚本
 * User: Joe
 * Date: 2017/7/4
 * Time: 上午10:44
 */

namespace Xt\Bin;


use Symfony\Component\Yaml\Yaml;
use DateTime;
use DateTimeZone;
use PDO;


ini_set("date.timezone", "PRC");
ini_set('memory_limit', -1);
ini_set('max_execution_time', '0');


class attach
{

    private $db;


    public function run()
    {
        $attach = '';
        $msg = '三生三世十里桃花-维护补偿';
        $this->app = 'HT_taohua';
        $this->lang = 'zh_CN';
        $zoneList = [103001, 103002];

        //$zoneList = $this->getZoneList();
        //$zoneList = array_keys($zoneList);


        foreach ($zoneList as $zone) {
            $userList = $this->getAllUser($zone);
            if (!$userList) {
                continue;
            }
            foreach ($userList as $user) {
                if (!$this->sendAttach($zone, $user, $attach, $msg)) {
                    // 失败日志
                    $this->logger("FAIL: $zone $user $attach");
                    continue;
                }

                // 成功日志
                $this->logger("SUCCESS: $zone $user $attach");
            }
        }

    }


    private function sendAttach($zone = '', $user = '', $attach = '', $msg = '')
    {
        if (!$zone || !$user || !$attach) {
            return false;
        }
        $appDateTime = date('Y-m-d H:i:s');

        $conn = $this->db($zone);

        $attachList = explode(',', $attach);
        foreach ($attachList as $attach) {
            if (strpos($attach, '*')) {
                list($att, $num) = explode('*', $attach);
            }
            else {
                $att = $attach;
                $num = 1;
            }
            for ($i = 0; $i < $num; $i++) {
                $conn->beginTransaction();
                try {
                    $sql = "INSERT INTO mail (`role_id`, `other_role`, `type`, `unread`, `gift`, `content`, `sent_time`) VALUES ('{$user}', '0', '1', '1', '{$att}', '{$msg}', '{$appDateTime}')";
                    $conn->exec($sql);

                    $lastInsertId = $conn->lastInsertId();
                    $sql = "INSERT INTO maillog(mid,roleid,datetime) VALUES($lastInsertId,$user,'$appDateTime')";
                    $conn->exec($sql);

                    $conn->commit();
                } catch (Exception $e) {
                    $conn->rollBack();
                    return false;
                }
            }
        }
        return true;
    }


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


    private function getAllUser($zone = '')
    {
        if (!$zone) {
            return false;
        }
        $sql = "SELECT role_id userId FROM role ORDER BY role_id DESC";
        $query = $this->db($zone)->query($sql);
        if (!$query) {
            return false;
        }
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return array_column($query->fetchAll(), 'userId');
    }


    private function db($handle = '')
    {
        if (!empty($this->db[$handle])) {
            return $this->db[$handle];
        }
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
        $this->db[$handle] = $db;
        return $db;
    }


    private function readAppConfig()
    {
        return Yaml::parse(file_get_contents(__DIR__ . "/../app/Config/{$this->app}/{$this->lang}.yml"));
    }


    private function logger($msg = '')
    {
        $this->createLog(date('Ymd') . '.txt', $msg);
        //print date('Y-m-d H:i:s O ') . $msg . "\r\n";
    }


    private function createLog($filename, $text)
    {
        if (strtolower(substr($filename, -3)) != 'txt') {
            $filename .= '.txt';
        }
        $filename = dirname(__FILE__) . '/' . $filename;
        if (!file_exists($filename)) {
            exec('touch ' . $filename);
            exec('chmod 777 ' . $filename);
        }
        $handle = fopen($filename, "a+b");
        $text .= "\r\n";
        fwrite($handle, $text);
        fclose($handle);
    }


}


include __DIR__ . '/../vendor/autoload.php';
$class = new attach();
$class->run();