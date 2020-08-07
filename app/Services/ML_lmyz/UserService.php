<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2018/11/12
 * Time: 3:14 PM
 */
/**
 * 用户相关
 * Class UserService
 */

namespace Xt\Rpc\Services\ML_lmyz;

use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Utils;

class UserService extends Service
{
    private $utilsModel;


    public function __construct($di)
    {
        parent::__construct($di);
        $this->utilsModel = new Utils();
    }

    /**
     * 用户信息
     * @param $parameter
     * @return mixed
     * http://v3-rpc.com/ML_lmyz/zh_CN/user/profile?zone=1006001&user_id=2&name=&account_id=
     */
    public function profile($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'missing parameter'
            ];
        }

        $deviceType = [
            '1' => 'Android',
            '2' => 'ios',
        ];

            if (!empty($parameter['user_id'])) {
                $sql = "SELECT
	Account.RoleID,
	Account.PlatformUID,
    Account.Privilege,
	Account.ServerId,
	Account.Privilege,
    Account.Device,
	Account.DeviceID,
	Account.CreateTime,
	Account.LogoutTime,
	BasicRes.PlayerLevel,
	BasicRes.PlayerName,
	BasicRes.Morale,
	BasicRes.AFKQuickTimes,
	BasicRes.ArenaHighRank,
    BasicRes.ArenaRegular,
    BasicRes.ResonanceStage,
    BasicRes.AreaLastStage,
    BasicRes.DemonWeeklyRank,
    BasicRes.LuckDraw,
    BasicRes.FightPow5Hero,
    BasicRes.Speak,
    BasicRes.VIP,
	Item.ItemType,
	Item.ItemNum 
FROM
	Account
	LEFT JOIN BasicRes ON Account.RoleID = BasicRes.RoleID
	LEFT JOIN Item ON Account.RoleID = Item.RoleID 
WHERE
    Account.RoleID = ?";
                $attribute = $this->gameDb($parameter['zone'])->fetchAll($sql, [$parameter['user_id']]);
            } elseif (!empty($parameter['account_id'])) {
                $sql = "SELECT
	Account.RoleID,
	Account.PlatformUID,
    Account.Privilege,
	Account.ServerId,
	Account.Privilege,
    Account.Device,
	Account.DeviceID,
	Account.CreateTime,
	Account.LogoutTime,
	BasicRes.PlayerLevel,
	BasicRes.PlayerName,
	BasicRes.Morale,
	BasicRes.AFKQuickTimes,
	BasicRes.ArenaHighRank,
    BasicRes.ArenaRegular,
    BasicRes.ResonanceStage,
    BasicRes.AreaLastStage,
    BasicRes.DemonWeeklyRank,
    BasicRes.LuckDraw,
    BasicRes.FightPow5Hero,
    BasicRes.Speak,
    BasicRes.VIP,
	Item.ItemType,
	Item.ItemNum 
FROM
	Account
	LEFT JOIN BasicRes ON Account.RoleID = BasicRes.RoleID
	LEFT JOIN Item ON Account.RoleID = Item.RoleID 
WHERE
    Account.PlatformUID = ?";
                $attribute = $this->gameDb($parameter['zone'])->fetchAll($sql, [$parameter['account_id']]);
            } elseif (!empty($parameter['name'])) {
                $name = $parameter['name'];
                $sql = "SELECT
                a.RoleID,
                a.PlatformUID,
                a.Privilege,
                a.ServerId,
                a.Privilege,
                a.Device,
                a.DeviceID,
                a.CreateTime,
                a.LogoutTime,
                b.PlayerLevel,
                b.PlayerName,
                b.Morale,
                b.AFKQuickTimes,
                b.ArenaHighRank,
                b.ArenaRegular,
                b.ResonanceStage,
                b.AreaLastStage,
                b.DemonWeeklyRank,
                b.LuckDraw,
                b.FightPow5Hero,
                b.Speak,
                b.VIP,
                i.ItemType,
                i.ItemNum 
            FROM
                BasicRes b
                LEFT JOIN Account a ON b.RoleID = a.RoleID
                LEFT JOIN Item i ON b.RoleID = i.RoleID 
            WHERE
                b.PlayerName LIKE '%{$name}%'";
                $attribute = $this->gameDb($parameter['zone'])->fetchAll($sql, [$parameter['name']]);
            }

        if (!$attribute) {
            return [
                'code' => 1,
                'msg' => 'no data',
            ];
        }

        if (count($attribute) == 1) {
            foreach ($attribute as $key => $value) {
                $result['account_id']      = trim($value['PlatformUID'], ';');                                      // user_id
                $result['user_id']         = trim($value['RoleID'], ';');                                           // role_id
                $result['server']          = trim($value['ServerId'], ';');                                         // 服务器id
                $result['name']            = trim($value['PlayerName'], ';');                                       // role_name
                if (!empty($value['ItemType']) && $value['ItemType'] == 2) {                                        // 钻石
                    $result['diamond'] = $value['ItemNum'];                                                 
                } else {
                    $result['diamond'] = 0;
                }

                if (!empty($value['ItemType']) && $value['ItemType'] == 5) {                                        // 金币
                    $result['coin'] = $value['ItemNum'];                                                           
                } else {
                    $result['coin'] = 0;
                }

                $result['level']           = explode(';', $value['PlayerLevel'])[0];                                // 等级
                $result['exp']             = explode(';', $value['PlayerLevel'])[1];                                // 经验值
                $result['morale']          = trim($value['Morale'], ';');                                           // 士气
                $result['arenaHighRank']   = trim($value['ArenaHighRank'], ';');                                    // 高阶竞技场点数
                $result['arenaRegular']    = trim($value['ArenaRegular'], ';');                                     // 普通竞技场点数
                $result['demonWeeklyRank'] = trim($value['DemonWeeklyRank'], ';');                                  // 心魔周榜积分
                $result['areaLastStage']   = trim($value['AreaLastStage'], ';');                                    // 最后通关关卡
                $result['luckDraw']        = trim($value['LuckDraw'], ';');                                         // 抽卡统计
                $result['LogoutTime']      = date('Y-m-d H:i:s', trim($value['LogoutTime'], ';'));                  // 登出时间
                $result['mPower']          = explode(';', $value['FightPow5Hero'])[0];                              // 战力
                $result['device']          = $deviceType[trim($value['Device'], ';')];                              // 设备系统
                $result['device_id']       = $value['DeviceID'];                                                    // 设备id
                $result['create_time']     = date('Y-m-d H:i:s', trim($value['CreateTime'], ';'));                  // 角色创建时间
                $result['quick_afkNum']    = trim($value['AFKQuickTimes'], ';');                                    // 今日快速挂机次数
                $result['GM_privilege']    = trim($value['Privilege'], ';');                                        // gm权限
                $result['vip_level']       = empty($value['VIP']) ? '' : explode(';', $value['VIP'])[0];            // vip等级
                $result['vip_exp']         = empty($value['VIP']) ? '' : explode(';', $value['VIP'])[1];            // vip经验
            }
        } else {
            for($i = 0; $i < count($attribute); $i++) {

            }
            foreach ($attribute as $k => $v) {
                // 钻石
                if (!empty($v['ItemType']) && $v['ItemNum'] == 2) {
                    $diamonds = $v['ItemNum'];
                }

                // 金币
                if (!empty($v['ItemType']) && $v['ItemNum'] == 5) {
                    $coin = $v['ItemNum'];
                }
                $result[] = [
                    'account_id'      => trim($v['PlatformUID'], ';'),
                    'user_id'         => trim($v['RoleID'], ';'),
                    'server'          => trim($v['ServerId'], ';'),
                    'name'            => trim($v['PlayerName'], ';'),
                    'diamonds'        => $diamonds,
                    'coin'            => $coin,
                    'level'           => explode(';', $v['PlayerLevel'])[0],
                    'exp'             => explode(';', $v['PlayerLevel'])[1],
                    'morale'          => trim($v['Morale'], ';'),
                    'arenaHighRank'   => trim($v['ArenaHighRank'], ';'),
                    'arenaRegular'    => trim($v['ArenaRegular'], ';'),
                    'demonWeeklyRank' => trim($v['DemonWeeklyRank'], ';'),
                    'areaLastStage'   => trim($v['AreaLastStage'], ';'),
                    'luckDraw'        => trim($v['LuckDraw'], ';'),
                    'LogoutTime'      => date('Y-m-d H:i:s', trim($v['LogoutTime'], ';')),
                    'mPower'          => explode(';', $v['FightPow5Hero'])[0],
                    'device'          => $deviceType[trim($v['Device'], ';')],
                    'device_id'       => $v['DeviceID'],
                    'create_time'     => date('Y-m-d H:i:s', trim($v['CreateTime'], ';')),
                    'quick_afkNum'    => trim($v['AFKQuickTimes'], ';'),
                    'GM_privilege'    => trim($v['Privilege'], ';'),
                    'vip_level'       => empty($v['VIP']) ? '' : explode(';', $v['VIP'])[0],
                    'vip_exp'         => empty($v['VIP']) ? '' : explode(';', $v['VIP'])[1]
                ]; 
            }
        }

        dump($result);exit;
        $count = count($attribute);

        return [
            'code' => 0,
            'msg' => 'success',
            'count' => $count,
            'data' => $result
        ];
    }

    /**
     * 用户道具查询
     * @param $parameter
     * @return mixed
     * http://v3-rpc.com/ML_lmyz/zh_CN/user/propinfo?zone=1006001&user_id=2&action_id=&status=2
     */
    public function propinfo($parameter){
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        $sql = "SELECT ItemId, ItemNum,ItemLeft, GetItem, Action FROM ItemLog WHERE 1=1 ";

        if (!empty($parameter['action_id'])) {
            $sql .= "AND Action in ({$parameter['action_id']}) ";
        }

        // 2: 代表 全部
        if ($parameter['status'] != 2) {
            $sql .= "AND GetItem = {$parameter['status']}";
        }

        if (!empty($parameter['user_id'])) {
            $sql .= "AND RoleID={$parameter['user_id']}";
            $attribute = $this->gameDb($parameter['zone'].'_log')->fetchAll($sql);
        }

        if (empty($attribute)) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $attribute
        ];
    }


    /**
     * 只查询用户name
     * @param $parameter
     */
    public function userinfo($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $sql = "";
        if (!empty($parameter['name'])) {
            $sql = "SELECT
	Account.PlatformUID,
	BasicRes.`name`,
	Account.RoleID 
FROM
	Account,
	BasicRes 
WHERE
	Account.RoleID = BasicRes.RoleID 
	AND BasicRes.`name` = '{$parameter['name']}'";
        } else {
            $sql = "SELECT
	Account.PlatformUID,
	BasicRes.`name`,
	Account.RoleID 
FROM
	Account,
	BasicRes 
WHERE
	Account.RoleID = BasicRes.RoleID 
	AND Account.RoleID = {$parameter['role_id']}";
        }

        try {
            $data = $this->gameDb($parameter['zone'])->fetchAll($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $data[0],
        ];
    }

    /**
     * 通过account_id或者角色名称换取role_id
     */
    public function getRoleId($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        try {
            if (!empty($parameter['account_id'])) {
                // 通过account_id查询role_id
                $sql = "SELECT RoleID FROM `Account` WHERE PlatformUID = {$parameter['account_id']}";
            } else if (!empty($parameter['user_name'])) {
                // 通过用户名查询role_id
                $sql = "SELECT RoleID FROM `BasicRes` WHERE `name`='{$parameter['user_name']}'";
            }

            $result = $this->gameDb($parameter['zone'])->fetchAssoc($sql);
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

    public function playerOffline($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        // 增加禁言用户: type = 1 移除禁言用户: type = 2 增加玩家封禁: type = 3 移除玩家封禁: type = 4
        $send['type'] = 3;
        $send['role_id'] = $parameter['role_id'];
        $send['end_time'] = $parameter['end'];
        $url = $this->di['db_cfg']['game_url']['banchat_url'];
        $send_data = [
            'data' => $send
        ];
        dump($url);exit;
        $response = $this->post($url, json_encode($send_data, true));
        return $response;
    }

    // 获取账号id
    public function getAccountId($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        try {
            if (!empty($parameter['role_id'])) {
                $sql = "SELECT `PlatformUID` FROM `Account` WHERE `RoleID`={$parameter['role_id']}";
            } elseif (!empty($parameter['name'])) {
                $sql = "SELECT
	a.`PlatformUID` 
FROM
`Account` a
	LEFT JOIN `BasicRes` b ON a.`RoleID` = b.`RoleID` 
	AND b.`name` = '{$parameter['name']}'";
            }

            $result = $this->gameDb($parameter['zone'])->fetchAssoc($sql);
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

    public function getRoleIdByName($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $roleId = $parameter['name'] . ';';
        try {
            $sql = "SELECT
            `RoleID` 
        FROM
            BasicRes 
        WHERE
            `PlayerName` = '{$roleId}'";
            $result = $this->gameDb($parameter['zone'])->fetchAssoc($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
        
        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $result['RoleID']
        ];
    }

    public function cancelPlayerBan($parameter) 
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed',
            ];
        }

        $url = $this->di['db_cfg']['game_url']['banchat_url'];

        $send_data = [
            'zone' => $parameter['zone'],
            'roleId' => $parameter['role_id'],
            'start_time' => '',
            'end_time' => ''
        ];

        dump($send_data);exit;
        // 直接发送请求给游戏服务端
        $result = $this->post($url, $send_data);
        return $result;
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
