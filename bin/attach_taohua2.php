<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2019/1/23
 * Time: 7:40 PM
 */

namespace Xt\Bin;

use Symfony\Component\Yaml\Yaml;
use PDO;

ini_set("date.timezone", "PRC");
ini_set('memory_limit', -1);
ini_set('max_execution_time', '0');

class attach_taohua2
{
    private $db;

    // app 名称
    public $app;

    // 三生三世2 语言版本
    public $lang;

    // 邮件发送标题 目前不需要
    public $title;

    // 邮件发送内容 目前不需要
    public $msg;

    // 补偿发送的服务器列表
    public $zoneList;

    // 礼包id '0001,0002'
    public $attch;

    public function run()
    {

        foreach ($this->zoneList as $k => $zone) {
            $roleList = $this->getAllRoles($zone);
            $roleList = [21530];
            foreach ($roleList as $role) {
                if (!$this->attch($zone, $role, $this->attch, $this->title, $this->msg)) {
                    $this->logger("FAIL: $zone $role $this->attch");
                    continue;
                }

                $this->logger("SUCCESS: $zone $role $this->attch");
            }
        }
    }

    /**
     * 查询每个服务器的全部role
     * @param $zone
     * @return bool|\PDOStatement
     */
    private function getAllRoles($zone)
    {
        if ($zone == '') {
            return false;
        }

        //todo 正式时 需要添加 WHERE role_id > 21499 机器人不发奖
        $sql = "SELECT `role_id` FROM `role`";
        $query = $this->getDb($zone)->query($sql);

        if (!$query) {
            return false;
        }
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $roles = array_column($query->fetchAll(), 'role_id');
        return $roles;
    }

    public function attch($zoneId, $roleId, $attch, $title, $msg)
    {
        if (!$zoneId || !$roleId || !$attch) {
            return false;
        }

        $dbCon = $this->getDb($zoneId);
        $sendT = time();

        $giftList = explode(',', $attch);
        foreach ($giftList as $gift) {
            if (strpos($gift, '*')) {
                list($att, $num) = explode('*', $gift);
            } else {
                $att = $gift;
                $num = 1;
            }

            for ($i = 0; $i < $num; $i++) {
                // 使用事物
                try {
                    $sql = "INSERT INTO `mail` (`role_id`, `type`, `title`, `content`, `read`, `sender_account_id`, `sender_role_id`, `sender_level`, `sender_vip_level`,  `sender_avatar`, `sender_gender`, `sender_fight_power`, `sender_role_name`,  `attachment`, `param1`, `param2`, `param3`, `param4`,`sent_time`) VALUES ('{$roleId}', 1, 10000, 10001, 0, 0, 0, 0, 0, 0, 0, 0, '', {$att}, 0, 0, 0, 0, {$sendT})";
                    $dbCon->exec($sql);
                } catch (\Exception $e) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 获取数据库事例
     * @param $zoneId
     * @return PDO
     */
    private function getDb($zoneId)
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
        $this->createLog('taohua2_' . date('Ymd') . '.txt', $msg);
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
        return Yaml::parse(file_get_contents(__DIR__ . "/../app/Config/{$this->app}/{$this->lang}.yml"));
    }
}

include __DIR__ . '/../vendor/autoload.php';
$class = new attach_taohua2();
$class->attch = '90001*1,2*3'; // 示例
$class->app = 'HT_taohua2';
$class->lang = 'zh_CN';
$class->zoneList = [1];
$class->run();