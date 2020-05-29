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

namespace Xt\Rpc\Services\HT_taohua2;

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
     * http://v3-rpc.com/HT_taohua2/zh_CN/user/profile?zone=1006001&user_id=2
     */
    public function profile($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'missing parameter'
            ];
        }

        $zone = $parameter['zone'];
        // 162: role 实时的货币type
        // 163: role 只会增加不会减少的货币type 用作vip积分
        try {
            if (!empty($parameter['user_id'])) {
                $sql = "SELECT * FROM `role` WHERE role_id = ?";
                $vipSql = "SELECT type, value coin FROM `money` WHERE role_id = ? AND type IN (162, 163)";
                $attribute = $this->gameDb($zone)->fetchAll($sql, [$parameter['user_id']]);
                $vip = $this->gameDb($zone)->fetchAll($vipSql, [$parameter['user_id']]);
            } elseif (!empty($parameter['account_id'])) {
                $sql = "SELECT * FROM `role` WHERE  account_id = ?";
                $attribute = $this->gameDb($zone)->fetchAll($sql, [$parameter['account_id']]);
                $vipSql = "SELECT type, value coin FROM `money` WHERE role_id = ? AND type IN (162, 163)";
                $vip = $this->gameDb($zone)->fetchAll($vipSql, [$attribute[0]['role_id']]);
            } elseif (!empty($parameter['name'])) {
                // 由于游戏库做了唯一索引
                $sql = "SELECT * FROM `role` WHERE name = ?";
                $attribute = $this->gameDb($zone)->fetchAll($sql, [$parameter['name']]);
                $vipSql = "SELECT type, value coin FROM `money` WHERE role_id = ? AND type IN (162, 163)";
                $vip = $this->gameDb($zone)->fetchAll($vipSql, [$attribute[0]['role_id']]);
            }
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => $e->getMessage()
            ];
        }

        if (!$attribute) {
            return [
                'code' => 1,
                'msg' => "no data"
            ];
        }

        // 数据合并
        foreach ($vip as $key => $value) {
            if ($value['type'] == 162) {
                $attribute[0]['coin'] = $value['coin'];
            } elseif ($value['type'] == 163) {
                $attribute[0]['vip'] = $value['coin'];
            }
        }
        foreach ($attribute as $key => $player) {
            $result[$key]['account_id'] = $player['account_id'];
            $result[$key]['user_id'] = $player['role_id'];
            $result[$key]['name'] = $player['name'];
            $result[$key]['coin'] = empty($player['coin']) ? 0 : $player['coin'];
            $result[$key]['vip'] = empty($player['vip']) ? 0 : $player['vip'];
            $result[$key]['level'] = $player['level'];
            $result[$key]['exp'] = $player['exp'];
            $result[$key]['create_time'] = date('Y-m-d H:i:s',$player['register_time']);
            $result[$key]['attribute'] = $player;
        }
        $count = count($result);
        if ($count == 1) {
            $result = $result['0'];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'count' => $count,
            'data' => $result
        ];
    }
}
