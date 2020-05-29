<?php
/**
 * 活动脚本
 * User: Joe
 * Date: 2017/7/4
 * Time: 上午10:44
 */

namespace Xt\Bin;


use Symfony\Component\Yaml\Yaml;
use DateTime;
use DateTimeZone;
use PDO;


ini_set("date.timezone", "UTC");
ini_set('memory_limit', -1);
ini_set('max_execution_time', '0');


class activityBin
{

    private $db;

    public function run()
    {
        $activityList = $this->getActivityList();

        if (!$activityList) {
            $this->logger('no activity list');
            exit();
        }

        // 循环活动
        foreach ($activityList as $activity) {
            $this->logger('ACT: ' . $activity['id']);

            $options = $this->getActivityDetail($activity['id']);
            if (!$options) {
                $this->logger('no activity config');
                continue;
            }

            // min step
            $min = 9999999;
            foreach ($options as $v) {
                if ($v['step'] < $min) {
                    $min = $v['step'];
                }
            }

            // 获取用户
            switch ($activity['type']) {
                case 'prepay':
                    $users = $this->soap('data/get_users_prepay', ['act_id' => $activity['id'], 'min' => $min]);
                    break;

                case 'spend':
                    $users = $this->soap('data/get_users_spend', ['act_id' => $activity['id'], 'min' => $min]);
                    break;

                case 'login':
                    $users = $this->soap('data/get_users_login', ['act_id' => $activity['id'], 'min' => $min]);
                    break;

                case 'level':
                    $users = $this->soap('data/get_users_level', ['act_id' => $activity['id'], 'min' => $min]);
                    break;

                default:
            }

            if ($users['code'] != 0 || empty($users['data'])) {
                $this->logger('no users');
                continue;
            }


            // 循环处理
            foreach ($users['data'] as $user => $step) {
                $this->logger('USER: ' . $user);
                foreach ($options as $option) {
                    if ($step < $option['step']) {
                        continue;
                    }
                    $this->send($user, $activity['id'], $option);
                }
            }
        }
    }


    private function send($user = '', $act_id = 0, $option)
    {
        $this->logger('PROP: ' . $option['prop']);

        // 忽略 - 已处理
        $sql = "SELECT id FROM logs_activity WHERE user_id='{$user}' AND item_id={$act_id} AND cfg_id={$option['id']}";
        $query = $this->db()->query($sql);
        if ($query->fetch()) {
            $this->logger('IGNORE');
            return false;
        }

        // 处理
        $tmpUser = explode('-', $user);
        $result = $this->soap('/prop/attach', [
            'zone'    => $tmpUser['0'],
            'user_id' => $tmpUser['1'],
            'attach'  => $option['prop'],
            'msg'     => $option['title']
        ]);
        if ($result['code'] != 0) {
            $this->logger('FAILED');
            return false;
        }

        // 日志
        $dateTime = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d h:i:s');;
        if (strlen($option['prop']) > 32) {
            $option['prop'] = substr($option['prop'], 0, 32);
        }
        $sql = "INSERT INTO logs_activity(item_id,cfg_id,user_id,prop,create_time) VALUES ($act_id,{$option['id']},'$user','{$option['prop']}','{$dateTime}')";
        $this->db()->exec($sql);
        $this->logger('OK');

        return true;
    }


    // 活动列表
    private function getActivityList()
    {
        $dateTime = (new \DateTime('now', new \DateTimeZone('UTC')))->format('Y-m-d h:i:s');
        $sql = "SELECT id,zone,channel,type,title FROM activity
WHERE status=1 AND start_time<'{$dateTime}' AND end_time>='{$dateTime}'";
        $query = $this->db()->query($sql);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $activityList = $query->fetchAll();
        return $activityList;
    }


    // 活动详细
    private function getActivityDetail($id = 0)
    {
        $sql = "SELECT id,step,prop,title FROM activity_cfg WHERE item_id={$id} ORDER BY step";
        $query = $this->db()->query($sql);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        return $query->fetchAll();
    }


    private function soap($method = '', $data = [])
    {
        $method = trim($method, '/');
        $tmp = explode('/', $method);
        $class = $tmp['0'];
        $method = $tmp['1'];

        // TODO :: url
        $host = 'http://rpc.localhost.com:8080';
        $protocol = 'yar';
        switch ($protocol) {
            case 'yar':
                $client = new \Yar_Client($host . "/yar/$class");
                return $client->$method($data);
                break;

            case 'soap':
                $client = new \SoapClient(null, array(
                    'location' => $host . "/soap/$class",
                    'uri'      => 'app',
                    'style'    => SOAP_RPC,
                    'use'      => SOAP_ENCODED,
                    'trace'    => true
                ));
                return $client->$method($data);
                break;
        }
    }


    /**
     * 连接数据库
     * @return PDO
     */
    private function db()
    {
        if (!empty($this->db)) {
            return $this->db;
        }
        $config = Yaml::parse(file_get_contents(__DIR__ . "/../app/Config/app.yml"));
        $params = $config['db_data'];

        $dsn = 'mysql:host=' . $params['host'] . ';port=' . $params['port'] . ';dbname=' . $params['db'];
        $db = new PDO($dsn, $params['user'], $params['pass']);
        $db->query('set names ' . $params['charset']);
        $this->db = $db;
        return $this->db;
    }


    private function logger(
        $msg = ''
    ) {
        print date('Y-m-d H:i:s O ') . $msg . "\r\n";
    }


}


include __DIR__ . '/../vendor/autoload.php';
$class = new activityBin();
$class->run();