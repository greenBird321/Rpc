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

            if (!empty($parameter['user_id'])) {
                $sql = "SELECT
	Account.RoleID,
	Account.PlatformUID,
	Account.ServerId,
	Account.GMPrivilege,
	Account.DeviceID,
	Account.CreateTime,
	Account.UpdateTime,
	Account.ActiveDay,
	Account.ban,
	BasicRes.PlayerLv,
	BasicRes.playerExp,
	BasicRes.`name`,
	BasicRes.morale,
	BasicRes.ArenaPoint,
	BasicRes.ArenaWinTime,
	BasicRes.GoodValue,
	BasicRes.RobValue,
	BasicRes.LogoutTime,
	BasicRes.MPower,
	BasicRes.WandLevel,
	BasicRes.QuickAFKNum,
	BasicRes.HighArenaPoint,
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
	Account.ServerId,
	Account.GMPrivilege,
	Account.DeviceID,
	Account.CreateTime,
	Account.UpdateTime,
	Account.ActiveDay,
	Account.ban,
	BasicRes.PlayerLv,
	BasicRes.playerExp,
	BasicRes.`name`,
	BasicRes.morale,
	BasicRes.ArenaPoint,
	BasicRes.ArenaWinTime,
	BasicRes.GoodValue,
	BasicRes.RobValue,
	BasicRes.LogoutTime,
	BasicRes.MPower,
	BasicRes.WandLevel,
	BasicRes.QuickAFKNum,
	BasicRes.HighArenaPoint,
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
                $sql = "SELECT
	Account.RoleID,
	Account.PlatformUID,
	Account.ServerId,
	Account.GMPrivilege,
	Account.DeviceID,
	Account.CreateTime,
	Account.UpdateTime,
	Account.ActiveDay,
	Account.ban,
	BasicRes.PlayerLv,
	BasicRes.playerExp,
	BasicRes.`name`,
	BasicRes.morale,
	BasicRes.ArenaPoint,
	BasicRes.ArenaWinTime,
	BasicRes.GoodValue,
	BasicRes.RobValue,
	BasicRes.LogoutTime,
	BasicRes.MPower,
	BasicRes.WandLevel,
	BasicRes.QuickAFKNum,
	BasicRes.HighArenaPoint,
	Item.ItemType,
	Item.ItemNum 
FROM
	Account
	LEFT JOIN BasicRes ON Account.RoleID = BasicRes.RoleID
	LEFT JOIN Item ON Account.RoleID = Item.RoleID
WHERE
	BasicRes.name = ?";;
                $attribute = $this->gameDb($parameter['zone'])->fetchAll($sql, [$parameter['name']]);
            }


        if (!$attribute) {
            return [
                'code' => 1,
                'msg' => 'no data',
            ];
        }

        foreach ($attribute as $key => $value) {
            $result['account_id']      = $value['PlatformUID'];                                     // user_id
            $result['user_id']         = $value['RoleID'];                                          // role_id
            $result['server']          = $value['ServerId'];                                        // 服务器id
            $result['name']            = $value['name'];                                            // role_name
            if (!empty($value['ItemType'])) {
                $result['money_type'][] = $value['ItemType'];                                        // 钱类型
            }
            if (!empty($value['ItemNum'])) {
                $result['money_num'][]     = $value['ItemNum'];                                         // 钱
            }
            $result['level']           = $value['PlayerLv'];                                        // 等级
            $result['exp']             = $value['playerExp'];                                       // 经验值
            $result['morale']          = $value['morale'];                                          // 士气
            $result['ArenaPoint']      = $value['ArenaPoint'];                                      // 高阶竞技场积分
            $result['ArenaWinTime']    = date("Y-m-d H:i:s",$value['ArenaWinTime']);                // 竞技场挑战时间（时间戳）
            $result['RobValue']        = $value['RobValue'];                                        // 掠夺值
            $result['GoodValue']       = $value['GoodValue'];                                       // 善良值
            $result['LogoutTime']      = date('Y-m-d H:i:s', $value['LogoutTime']);                 // 登出时间
            $result['MPower']          = $value['MPower'];                                          // 战力
            $result['WandLevel']       = $value['WandLevel'];                                       // 鼓舞系统星级
            $result['device_id']       = $value['DeviceID'];                                        // 设备id
            $result['create_time']     = date('Y-m-d H:i:s', $value['CreateTime']);                 // 角色创建时间
            $result['active_day']      = $value['ActiveDay'];                                       // 活跃天数
            $result['ban']             = $value['ban'];                                             // 账号封禁
            $result['quick_afkNum']    = $value['QuickAFKNum'];                                     // 今日快速挂机次数
            $result['high_arenaPoint'] = $value['HighArenaPoint'];                                  // 高阶竞技场积分
            $result['GM_privilege']    = $value['GMPrivilege'];                                     // gm权限
        }

        $count = 1;

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
        if (empty($paramter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
        // 增加禁言用户: type = 1 移除禁言用户: type = 2 玩家踢下线: type = 3
        $send['type'] = 3;
        $send['role_id'] = $paramter['role_id'];
        $send['start_time'] = '';
        $send['end_time'] = '';
        $url = $this->di['db_cfg']['game_url']['banchat_url'];
        dump($url);exit;
        $response = $this->post($url, json_encode($send, true));
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
