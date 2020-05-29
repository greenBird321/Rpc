<?php

namespace Xt\Rpc\Models;


use Xt\Rpc\Core\Model;

class Trade extends Model
{

    /**
     * 检查是否首充
     * @param $user_id
     * @param $product_id
     * @return bool
     */
    public function checkFirstPurchase($user_id, $product_id)
    {
        $sql = "SELECT id FROM logs_purchase WHERE user_id=? AND product_id=?";
        $bind = [$user_id, $product_id];
        $response = $this->db_data->fetchAssoc($sql, $bind);
        if ($response) {
            return true;
        }
        return false;
    }


    public function getDevice($user_id = 0)
    {
        $sql = "SELECT uuid,adid,device,version,channel,ip FROM users_login_201706 WHERE user_id=? ORDER BY id DESC";
        $bind[] = $user_id;
        try {
            $result = $this->db_logs->fetchAssoc($sql, $bind);
        } catch (\Exception $e) {
            return false;
        }
        if (!$result) {
            return false;
        }
        return $result;
    }

}