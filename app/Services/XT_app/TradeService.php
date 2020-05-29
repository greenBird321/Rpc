<?php

/**
 * 充值相关
 * Class Trade
 */
namespace Xt\Rpc\Services\XT_app;


use Exception;
use Xt\Rpc\Core\Service;

class TradeService extends Service
{


    public function __construct($di)
    {
        parent::__construct($di);
    }


    public function purchase($parameter)
    {
        // 暂不检查签名
        unset($parameter['timestamp'], $parameter['sign']);
        if (strpos($parameter['user_id'], '-')) {
            list($zone, $user_id) = explode('-', $parameter['user_id']);
        }
        else {
            $zone = 0;
            $user_id = $parameter['user_id'];
        }


        // 当前RPC系统时间
        $dateTimeNow = date('Y-m-d H:i:s');
        // 当前游戏时间
        $appDateTime = $this->utilsModel->switchTimeZone($this->di['db_cfg']['setting']['timezone'],
            $this->di['config']['setting']['timezone']);


        // 检查订单
        $sql = "SELECT id FROM logs_purchase WHERE transaction=?";
        if ($this->db_data->fetchAssoc($sql, [$parameter['transaction']])) {
            return false;
        }


        // 事务
        $this->db_data->beginTransaction();
        try {
            // TODO :: 写入游戏数据

            // 写入日志数据
            $parameter['create_time'] = date('Y-m-d H:i:s');
            $this->db_data->insert('logs_purchase', $parameter);
            $this->db_data->commit();
        } catch (Exception $e) {
            $this->db_data->rollBack();
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success'
        ];
    }

}