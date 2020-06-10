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

        try {
            if (!empty($parameter['user_id'])) {
                $sql = "SELECT * FROM `t_game_user` WHERE user_id = ?";
                $attribute = $this->gameDb(0)->fetchAll($sql, [$parameter['user_id']]);
            } else if (!empty($parameter['account_id'])) {
                $sql = "SELECT * FROM `t_game_user` WHERE account_id = ?";
                $attribute = $this->gameDb(0)->fetchAll($sql, [$parameter['account_id']]);
            } else if (!empty($parameter['name'])) {
                $sql = "SELECT * FROM `t_game_user` WHERE user_name = ?";
                $attribute = $this->gameDb(0)->fetchAll($sql, [$parameter['name']]);
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
                'msg' => 'no data',
            ];
        }

        foreach ($attribute as $key => $value) {
            $result[$key]['account_id'] = $value['account_id'];
            $result[$key]['user_id'] = $value['user_id'];
            $result[$key]['name'] = $value['user_name'];
            $result[$key]['coin'] = $value['user_gold'];
            $result[$key]['vip'] = $value['user_money'];
            $result[$key]['level'] = $value['user_lv'];
            $result[$key]['exp'] = $value['user_exp'];
            $result[$key]['create_time'] = $value['user_create_time'];
            $result[$key]['attribute'] = $value;
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
