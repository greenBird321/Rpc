<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2020/6/22
 * Time: 8:14 PM
 */

namespace Xt\Rpc\Services\ML_lmyz;

use Xt\Rpc\Models\Game;

class GameService extends \Xt\Rpc\Services\XT_app\GameService
{
    private $gameModel;

    public function __construct($di)
    {
        parent::__construct($di);
        $this->gameModel = new Game();
    }

    public function getConsumeList($parameter)
    {
        if (empty($parameter['serverId'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        try {
            $sql    = "SELECT
	SUM( ItemNum ) diamonds,
	`Action` actionId 
FROM
	ItemLog 
WHERE
	GetItem = 0 
	AND ActionTime BETWEEN {$parameter['start']}
	AND {$parameter['end']}
GROUP BY
	actionId 
ORDER BY
	diamonds DESC";
            $result = $this->gameDb($parameter['serverId'] . '_log')->fetchAll($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $result
        ];
    }

    public function shopRanking($parameter)
    {
        if (empty($parameter['serverId'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $start = strtotime($parameter['start']);
        $end   = strtotime($parameter['end']);

        try {
            $sql = "
SELECT
	Action,
	ItemId,
	sum(ItemNum) buy_propNum
FROM
	ItemLog 
WHERE
	GetItem = 1 
	AND Action IN ( 100001, 100002, 100005, 100011, 100012 ) 
	AND ActionTime BETWEEN {$start} 
	AND {$end} 
	GROUP BY
	Action,ItemId
	ORDER BY
	buy_propNum DESC
";

            $result = $this->gameDb($parameter['serverId'] . '_log')->fetchAll($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $result
        ];
    }

    public function productRanking($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $start = strtotime($parameter['start']);
        $end   = strtotime($parameter['end']);

        try {
            $sql = "SELECT
	Action,
	ItemId ,
	SUM( ItemNum ) buy_num
FROM
	`ItemLog` 
WHERE
	GetItem = 1 
	AND Action IN ( 100001, 100002, 100005, 100011, 100012 ) 
	AND ActionTime BETWEEN {$start} 
	AND {$end} 
GROUP BY
	ItemId 
ORDER BY
	buy_num DESC 
	LIMIT 50";
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
        $result = $this->gameDb($parameter['zone'] . '_log')->fetchAll($sql);

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $result
        ];
    }

    public function userCreateTimes($parameter)
    {
        if (empty($parameter['serverId'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $start = date('Y-m-d H:00:00', $parameter['start']);
        $end   = date('Y-m-d H:00:00', $parameter['end']);
        // 将时间戳转换为最小单位为小时
        $start_hour = strtotime($start);
        $end_hour   = strtotime($end);

        try {
            $sql = "SELECT
    RoleID,
	CreateTime
FROM
	Account 
WHERE
	CreateTime BETWEEN {$parameter['start']} 
	AND {$parameter['end']}";

            $result = $this->gameDb($parameter['serverId'])->fetchAll($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        // CreateTime为key , RoleID为value
        if (!empty($result)) {
            foreach ($result as $r => $s) {
                $tmp[$s['CreateTime']] = $s['RoleID'];
            }
        } else {
            $tmp = [];
        }

        $data = [];
        for ($i = $start_hour; $i <= $end_hour; $i += 3600) {
            $k = 0;
            foreach ($tmp as $key => $value) {
                if ($key >= $i && $key <= $i + 3600) {
                    $k++;
                    $data[$i] = $k;
                }
            }

            // 数据补全
            if (!isset($data[$i])) {
                $data[$i] = '-';
            }
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $data
        ];
    }

    public function getWhiteList()
    {
        try {
            $sql    = "SELECT * FROM `WhiteList`";
            $result = $this->gameDb(1)->fetchAll($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $result
        ];
    }

    public function addWhiteList($parameter)
    {
        try {
            $sql    = "INSERT INTO `WhiteList` ( `IP`, `flag` )
VALUES
	(
	'{$parameter['ip']}',
	{$parameter['flag']})";
            $result = $this->gameDb(1)->exec($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        if (!$result) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg' => 'success'
        ];
    }

    public function deleteWhiteList($parameter)
    {
        try {
            $sql    = "DELETE FROM WhiteList WHERE AutoId = {$parameter['id']}";
            $result = $this->gameDb(1)->exec($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        if (!$result) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg' => 'success'
        ];
    }

    public function realTimeData($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        $date  = date("Y-m-d", time());
        $start = strtotime($date . ' 00:00:00');
        $end   = strtotime($date . ' 23:59:59');

        $loginSql = "";
    }

    public function top($parameter)
    {
        if (empty($parameter['zone']) && empty($parameter['channel'])) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }
        try {
            $result = $this->gameModel->top($parameter);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $result
        ];
    }

    public function distribution($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }
        try {
            $result = $this->gameModel->distribution($parameter);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $result
        ];
    }

    // 宏观 && 微观
    public function rechargeDistribution($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        try {
            $player_sql               = "SELECT COUNT(1) playCount FROM `Account`";
            $parameter['playerCount'] = $this->gameDb($parameter['zone'])->fetchAssoc($player_sql)['playCount'];
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        // 微观
        $info                = $this->gameModel->rechargeDistributionInfo($parameter);
        $info['rechargeAmount'] = empty($info['rechargeAmount']) ? 0 : $info['rechargeAmount'];
        $info['playerCount'] = $parameter['playerCount'];
        $info['rechargeMix'] = 0.00;
        if ($info['rechargeAmount'] || $info['rechargePlayerCount']) {
            $info['rechargeMix'] = round($info['rechargeAmount'] / $info['rechargePlayerCount'], 2);
        }

        $info['playerAverage'] = '0.0%';
        if ($info['rechargePlayerCount'] || $info['playerCount']) {
            $info['playerAverage'] = round($info['rechargePlayerCount'] / $info['playerCount'], 3) * 100 . '%';
        }

        $info['server'] = $parameter['zone'];
        
        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $info
        ];
    }

    public function sendGmData($parameter)
    {
        $url = 'http://148.70.170.239:8089/GmCmdRequest';
        $send = [
            'server' => $parameter['server'],
            'data' => [
                'command' => explode(',' , $parameter['gm'])
            ],
        ];
        $result = $this->post($url, json_encode($send, true));
        return $result;
    }

    public function lostPlayer($parameter)
    {
        if (empty($parameter['server']))
        {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        // 获取时间段创建总的用户数
        try {
            $countSql = "SELECT
	RoleID 
FROM
	Account 
WHERE
	CreateTime BETWEEN {$parameter['start']} 
	AND {$parameter['end']}";
            $roleIds = $this->gameDb($parameter['zone'])->fetchAssoc($countSql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }
        // 新建总人数
        $newCount = count($roleIds);

        // 获取流失用户数
        try {
            $lostSql = "";
            $lostRoleIds = $this->gameDb($parameter['zone'])->fetchAssoc($lostSql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
    }

    public function realtimeOnline($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
        $start = $parameter['start'];
        $end   = $parameter['end'];

        try {
            $sql    = "SELECT
	FROM_UNIXTIME( LogTime) times,
	OnlineNum 
FROM
	OnlineRecord 
WHERE
	LogTime >= {$start}
	AND LogTime <= {$end}
	LIMIT 1";
            $online = $this->gameDb($parameter['zone'] . '_log')->fetchAssoc($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
        // 游戏心跳失败或者数据库错误
        if (!$online) {
            return [
                'code' => 0,
                'msg' => 'success',
                'data' => [
                    'server' => $parameter['zone'],
                    'time' => date('Y-m-d H:i:s', $start),
                    'OnlineNum' => 0,
                ]
            ];
        } else {
            return [
                'code' => 0,
                'msg' => 'success',
                'data' => [
                    'server' => $parameter['zone'],
                    'time' => $online['times'],
                    'OnlineNum' => $online['OnlineNum']
                ],
            ];
        }
    }

    public function historyOnline($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $start = $parameter['start'];
        $end   = $parameter['end'];

        try {
            $sql = "SELECT
	DATE_FORMAT( FROM_UNIXTIME( LogTime, '%Y-%m-%d %H:%i:%s' ), '%Y-%m-%d %H' ) hours,
	Max( OnlineNum )  max_user_online,
	MIN( OnlineNum )  min_user_online
FROM
	OnlineRecord 
WHERE
	LogTime BETWEEN {$start}
	AND {$end}
GROUP BY
	hours";
            $historyData = $this->gameDb($parameter['zone']. '_log')->fetchAll($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
        $result = [];
        // 整理输出格式
        foreach ($historyData as $key => $value) {
            $result[$key]['server'] = $parameter['zone'];
            $result[$key]['hours'] = $value['hours'];
            $result[$key]['max_user_online'] = $value['max_user_online'];
            $result[$key]['min_user_online'] = $value['min_user_online'];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $result,
        ];
    }

    public function addBanChatPlayer($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
        // 增加禁言用户: type = 1 移除禁言用户: type = 2 增加账号封禁: type = 3
        $parameter['type'] = 1;
        $url = $this->di['db_cfg']['game_url']['banchat_url'];
        dump($url, json_encode($parameter, true));exit;
        $response = $this->post($url, json_encode($parameter, true));
        return $response;
    }

    public function removeBanPlayer($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        // 增加禁言用户: type = 1 移除禁言用户: type = 2 玩家踢下线: type = 3
        $parameter['type'] = 2;
        $parameter['start_time'] = '';
        $parameter['end_time'] = '';
        $url = $this->di['db_cfg']['game_url']['banchat_url'];
        dump($url, json_encode($parameter, true));exit;
        $response = $this->post($url,json_encode($parameter, true));
        return $response;
    }

    /**
     * 发送请求
     * @param $url
     * @param $data
     */
    public function post($url, $data)
    {

        //初使化init方法
        $ch = curl_init();

        //指定URL
        curl_setopt($ch, CURLOPT_URL, $url);

        //设定请求后返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //声明使用POST方式来进行发送
        curl_setopt($ch, CURLOPT_POST, 1);

        //发送什么数据呢
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


        //忽略证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //忽略header头信息
        curl_setopt($ch, CURLOPT_HEADER, 0);

        //设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        //发送请求
        $output = curl_exec($ch);

        //关闭curl
        curl_close($ch);

        //返回数据
        return $output;
    }
}