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

ini_set("date.timezone", "UTC");
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
//        foreach ($this->zoneList as $zoneId) {
//            $roles = $this->getAllRole($zoneId);
//            foreach ($roles as $role) {
//                // 发奖
//                if (!$this->attach($zoneId, $role, $this->attach, $this->title, $this->content)) {
//                    $this->logger("FAIL: $zoneId $role $this->attach");
//                    continue;
//                }
//                $this->logger("SUCCESS: $zoneId $role $this->attach");
//            }
//        }

        foreach ($this->zoneList as $zoneId) {
            if (!$this->attach($zoneId, $this->attach, $this->title, $this->content)) {
                $this->logger("FAIL: 群发失败  zoneId = $zoneId  $this->attach");
                continue;
            }
            $this->logger("SUCCESS: 群发成功没毛病 zoneId = $zoneId  $this->attach");
        }
    }

    // 发奖
    public function attach($zoneId, $attach, $title, $msg)
    {
        if (!$zoneId || !$attach) {
            return false;
        }

        $attachList = explode(',', $attach);
        $db = $this->getGameDb($zoneId);
        $send_time = date('Y-m-d H:i:s');
        // 处理奖品过多问题
        $count = 4;
        if (count($attachList) > $count + 1) {
            $attach_1 = '';
            $attach_2 = '';
            // 切分attach
            for ($i = 0; $i < count($attachList); $i++) {
                if ($i <= $count) {
                    $attach_1 .= str_replace('*', ',', $attachList[$i]).',';
                } elseif ($i > $count) {
                    $attach_2 .= str_replace('*', ',', $attachList[$i]).',';
                }
            }
            $attach_1 = trim($attach_1, ',');
            $attach_2 = trim($attach_2, ',');
            var_dump($attach_1);var_dump($attach_2);exit;
            try {
                $sql1 = <<<END
INSERT INTO `t_game_mail`
(`sender`, `sender_name`, `receiver`, `send_time`, `mail_type`, `award`, `title`,`content`)
VALUES
(0, '雷弥', 0, '{$send_time}', 5, '{$attach_1}', '{$title}', '{$msg}')
END;

                $sql2 = <<<END
INSERT INTO `t_game_mail`
(`sender`, `sender_name`, `receiver`, `send_time`, `mail_type`, `award`, `title`,`content`)
VALUES
(0, '雷弥', 0, '{$send_time}', 5, '{$attach_2}', '{$title}', '{$msg}')
END;
                $db->exec($sql1);
                $db->exec($sql2);
            } catch (\Exception $e) {
                return false;
            }
        } else {
            $attachList = str_replace('*', ',', $attach);
            try {
                $sql = <<<END
INSERT INTO `t_game_mail`
(`sender`, `sender_name`, `receiver`, `send_time`, `mail_type`, `award`, `title`,`content`)
VALUES
(0, '雷弥', 0, '{$send_time}', 0, '{$attachList}', '{$title}', '{$msg}')
END;
                $db->exec($sql);
            } catch (\Exception $e) {
                return false;
            }
        }

        //$attachList = str_replace('*', ',', $attach);
        //$db = $this->getGameDb($zoneId);
        //$send_time = date('Y-m-d H:i:s');
        /**
         *  t_game_mail:
         *  receiver:
         *  0:代表群发，xxxx具体值: 代表给个人发
         *  mail_type:
         *  0: 群发(邮件发送后不管注册时间，只要登录就可以获得)，群发只需要加一条即可。1: 单发， 2:好友留言，3:加好友-没用到 4:好友送时装 5:在发送时间前创建的玩家啊都可以收到邮件(定时脚本需要选择这个)
         *  system_id：
         *  -1: 默认值，删除邮件才会使用 0:未读 1:已读 2:已经领奖 3: 给玩家添加的系统群发邮件，已删除的状态
         */
//        try{
//            $sql = <<<END
//INSERT INTO `t_game_mail`
//(`sender`, `sender_name`, `receiver`, `send_time`, `mail_type`, `award`, `title`,`content`)
//VALUES
//(0, '伊妮莉丝制作委员会', 0, '{$send_time}', 0, '{$attachList}', '{$title}', '{$msg}')
//END;
//            var_dump($sql);
//            exit;
//            $db->exec($sql);
//        } catch (\Exception $e) {
//            return false;
//        }

        return true;
    }

    // 获取所有roleId
    public function getAllRole($zoneId)
    {
        $sql = "SELECT account_id FROM t_account";
        $query = $this->getGameDb($zoneId)->query($sql);
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
$ynls_attach->attach = '2*50000,20070003*20,20000005*45,20000006*60,20000007*90,20000008*120,20000014*45,20000015*60,20000016*90,20000017*120,20000023*75,20000024*100,20000025*150
,20000026*200,20000032*15,20000033*20,20000034*30,20000035*40,20000041*15,20000042*20,20000043*30,20000044*40';
$ynls_attach->title = '弦月测试补给time!';
$ynls_attach->content = '热烈庆祝苹果爸爸过审，特此给全服寓目者小福利~';
$ynls_attach->run();