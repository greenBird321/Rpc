<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2019/9/23
 * Time: 6:16 PM
 */

namespace Xt\Bin;

use Symfony\Component\Yaml\Yaml;
use PDO;

ini_set("date.timezone", "PRC");
ini_set('memory_limit', -1);
ini_set('max_execution_time', '0');

class attach_ynls
{
    private $db;

    public $app;
    // 语言版本
    public $lang;
    // 邮件标题
    public $title;
    // 邮件内容
    public $content;
    // 奖励id
    public $attach;
    // 补偿发送的服务器列表
    public $zoneList;

    public function run()
    {
        foreach ($this->zoneList as $zoneId) {
            $roles = $this->getAllRole($zoneId);
            foreach ($roles as $role) {
                // 发奖
                if (!$this->attach($zoneId, $role, $this->attach, $this->title, $this->content)) {
                    $this->logger("FAIL: $zoneId $role $this->attach");
                    continue;
                }
                $this->logger("SUCCESS: $zoneId $role $this->attach");
            }
        }
    }

    // 发奖
    public function attach($zoneId, $roleId, $attach, $title, $msg)
    {
        if (!$zoneId || !$attach || !$roleId) {
            return false;
        }

        $attachList = str_replace('*', ',', $attach);
        $db = $this->getGameDb($zoneId);
        $send_time = date('Y-m-d H:i:s');
        try{
            $sql = <<<END
INSERT INTO `t_game_mail` 
(`sender`, `sender_name`, `receiver`, `send_time`, `mail_type`, `award`, `title`,`content`) 
VALUES 
(0, 'system', {$roleId}, '{$send_time}', 0, '{$attachList}', '{$title}', '{$msg}')
END;
            $db->exec($sql);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    // 获取所有roleId
    public function getAllRole($zoneId)
    {
        $db = $this->getGameDb($zoneId);

        $sql = "SELECT  SQL_CALC_FOUND_ROWS account_id FROM t_account WHERE reg_dt BETWEEN '2019-09-05 00:00:00' AND  '2019-09-05 23:59:59' limit 10";
        //$query = $this->getGameDb($zoneId)->query($sql);
        $data = $db->query($sql);
        $data->setFetchMode(PDO::FETCH_ASSOC);
        $res = $data->fetchAll();
        $num = $db->query('SELECT FOUND_ROWS()');
        $num->setFetchMode(PDO::FETCH_ASSOC);
        $res1 = $num->fetchAll();
        dump($res, $res1);exit;
        if (!$query) {
            return false;
        }

        $query->setFetchMode(PDO::FETCH_ASSOC);
        $roles = array_column($query->fetchAll(), 'account_id');
        return $roles;
    }

    // 获取游戏数据实例
    private function getGameDb($zoneId)
    {
        $config = $this->readGameDb();
        $params = $config[$zoneId];
        $dsn = 'mysql:host=' . $params['host'] . ';port=' . $params['port'] . ';dbname=' . $params['db'];
        $db = new PDO($dsn, $params['user'], $params['pass']);
        $db->query('set names ' . $params['charset']);
        $this->db[$zoneId] = $db;
        return $db;
    }

    /**
     * 日志记录
     * @param string $msg
     */
    private function logger($msg = '')
    {
        $this->createLog('ynls_' . date('Ymd') . '.txt', $msg);
        //print date('Y-m-d H:i:s O ') . $msg . "\r\n";
    }

    /**
     *  创建日志文件
     * @param $filename
     * @param $text
     */
    private function createLog($filename, $text)
    {
        if (strtolower(substr($filename, -3)) != 'txt') {
            $filename .= '.txt';
        }
        $dir = dirname(__FILE__) . '/log';
        $filename = $dir . '/' . $filename;

        if (!is_dir($dir)) {
            exec('mkdir ' . $dir);
            exec('chmod 777 ' . $dir);
        }

        if (!file_exists($filename)) {
            exec('touch ' . $filename);
            exec('chmod 777 ' . $filename);
        }
        $handle = fopen($filename, "a+b");
        $text .= "\r\n";
        fwrite($handle, $text);
        fclose($handle);
    }

    /**
     * 读取配置文件
     * @return mixed
     */
    private function readGameDb()
    {
        return Yaml::parseFile(__DIR__ . "/../app/Config/{$this->app}/{$this->lang}.yml");
    }
}

include __DIR__ . '/../vendor/autoload.php';
$ynls_attach = new attach_ynls();
$ynls_attach->lang = 'zh_CN';
$ynls_attach->app = 'HT_ynls';
$ynls_attach->zoneList = [1];
$ynls_attach->attach = '8000*3,1000*2';
$ynls_attach->title = '这是一个测试标题';
$ynls_attach->content = '测试邮件功能是否好使';
$ynls_attach->run();