<?php

/**
 * 用户相关
 * Class UserService
 */
namespace Xt\Rpc\Services\HT_taohua;


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
     */
    public function profile($parameter)
    {
        if (empty($parameter['zone'])) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }
        $zone = $parameter['zone'];


        // 查询
        if (!empty($parameter['user_id'])) {
            $sql = "SELECT * FROM role WHERE role_id=?";
            $attribute = $this->gameDb($zone)->fetchAll($sql, [$parameter['user_id']]);
        }
        elseif (!empty($parameter['account_id'])) {
            $sql = "SELECT * FROM role WHERE account_id =? LIMIT 20";
            $attribute = $this->gameDb($zone)->fetchAll($sql, [$parameter['account_id']]);
        }
        elseif (!empty($parameter['name'])) {
            $sql = "SELECT * FROM role WHERE name LIKE '%{$parameter['name']}%' LIMIT 20";
            $attribute = $this->gameDb($zone)->fetchAll($sql);
        }
        if (!$attribute) {
            return [
                'code' => 1,
                'msg'  => 'no data'
            ];
        }


        foreach ($attribute as $key => $player) {
            $result[$key]['account_id'] = $player['account_id'];
            $result[$key]['user_id'] = $player['role_id'];
            $result[$key]['name'] = $player['name'];
            $result[$key]['coin'] = $player['gold'];
            $result[$key]['vip'] = $player['total_gold'];
            $result[$key]['level'] = $player['level'];
            $result[$key]['exp'] = $player['exp'];
            $result[$key]['create_time'] = $this->utilsModel->switchTimeZone(
                $this->di['config']['setting']['timezone'], $this->di['db_cfg']['setting']['timezone'], $player['register_time'], 'Y-m-d H:i:s O');
            $result[$key]['attribute'] = $player;
        }

        $count = count($result);
        if ($count == 1) {
            $result = $result['0'];
        }
        return [
            'code'  => 0,
            'msg'   => 'success',
            'count' => $count,
            'data'  => $result
        ];
    }

}