<?php

/**
 * 数据接口
 * Class DataService
 */
namespace Xt\Rpc\Services\HT_haizei;


class DataService extends \Xt\Rpc\Services\XT_app\DataService
{

    public function get_users_level($parameter)
    {
        empty($parameter['act_id']) ? $parameter['act_id'] = 0 : null;
        empty($parameter['zone']) ? $parameter['zone'] = '' : null;
        empty($parameter['min']) ? $parameter['min'] = 0 : null;
        empty($parameter['max']) ? $parameter['max'] = 10000 : null;

        $result = [];
        foreach ($this->di['db_cfg'] as $zone => $config) {
            // 过滤
            if (intval($zone) == 0) {
                continue;
            }
            if ($parameter['zone'] && $parameter['zone'] != $zone) {
                continue;
            }

            // 查询
            $sql = "SELECT CONCAT($zone,'-',role_id) user_id,level FROM `role` WHERE 1=1";
            if ($parameter['min']) {
                $sql .= " AND level>=" . $parameter['min'];
            }
            if ($parameter['max'] && $parameter['max'] < 10000) {
                $sql .= " AND level<=" . $parameter['max'];
            }
            $lists = $this->gameDb($zone)->fetchAll($sql);

            if (!$lists) {
                continue;
            }
            foreach ($lists as $user) {
                $result[$user['user_id']] = $user['level'];
            }
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $result
        ];
    }

}