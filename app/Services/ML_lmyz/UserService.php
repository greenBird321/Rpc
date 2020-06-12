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
            $result['money_type'][]    = $value['ItemType'];                                        // 钱类型
            $result['money_num'][]     = $value['ItemNum'];                                         // 钱
            $result['level']           = $value['PlayerLv'];                                        // 等级
            $result['exp']             = $value['playerExp'];                                       // 经验值
            $result['morale']          = $value['morale'];                                          // 士气
            $result['ArenaPoint']      = $value['ArenaPoint'];                                      // 高阶竞技场积分
            $result['ArenaWinTime']    = date("Y-m-d H:i:s",$value['ArenaWinTime']);         // 竞技场挑战时间（时间戳）
            $result['RobValue']        = $value['RobValue'];                                        // 掠夺值
            $result['GoodValue']       = $value['GoodValue'];                                       // 善良值
            $result['LogoutTime']      = date('Y-m-d H:i:s', $value['LogoutTime']);          // 登出时间
            $result['MPower']          = $value['MPower'];                                          // 战力
            $result['WandLevel']       = $value['WandLevel'];                                       // 鼓舞系统星级
            $result['device_id']       = $value['DeviceID'];                                        // 设备id
            $result['create_time']     = date('Y-m-d H:i:s', $value['CreateTime']);          // 角色创建时间
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
}
