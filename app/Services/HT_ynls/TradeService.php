<?php
namespace Xt\Rpc\Services\HT_ynls;

use Exception;
use Xt\Rpc\Models\Trade;
use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Utils;

class TradeService extends Service
{

    private $utilsModel;

    private $tradeModel;

    public function __construct($di)
    {
        parent::__construct($di);
        $this->utilsModel = new Utils();
        $this->tradeModel = new Trade();
    }

    public function purchase($parameter)
    {
        //暂不检查签名
        unset($parameter['sign'], $parameter['timestamp']);
        if (!strpos($parameter['user_id'], '-')) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }
        list($zone, $user_id) = explode('-', $parameter['user_id']);

        // rpc系统时间
        $dateTimeNow = date('Y-m-d H:i:s');

        $logTime = time();

        // 游戏时间
        // $appDataTime = $this->utilsModel->switchTimeZone($this->di['db_cfg']['setting']['timezone'], $this->di['config']['setting']['timezone']);


        // 检查订单
        $sql = "SELECT id FROM logs_purchase WHERE transaction=?";
        if ($this->db_data->fetchAssoc($sql, [$parameter['transaction']])) {
            $this->di['logger']->error('purchase error - transaction exist', $parameter);
            return ['code' => 0, 'msg' => 'success'];
        }

        // 检查产品
        $sql = "SELECT product_id,type,name,gateway,price,currency,coin,custom FROM products WHERE product_id=? AND  status=1";
        $bind[] = $parameter['product_id'];
        $product = $this->db_data->fetchAssoc($sql, $bind);
        if (!$product) {
            $this->di['logger']->error('purchase error - no product', $parameter);
            return false;
        }
        $game_product_id = $product['custom'];
        $price = $product['price'];
        $game_connect = $this->gameDb($zone);
        $game_trade_format = $parameter['transaction']; // 兼容游戏订单格式
        $event_id = $parameter['ext'];
        try {
                $sql = "INSERT INTO gold_order (role_id, product_id, pay_method, real_money ,billing_id , log_time,event_id) VALUES ('$user_id','$game_product_id', 'web', '$price','$game_trade_format' , '$logTime',$event_id)";
                $game_connect->executeUpdate($sql);
        } catch (Exception $e) {
            $this->di['logger']->error('purchase error', $parameter);
            return [
                'code' => 1,
                'msg'  => 'failed, game server error'
            ];
        }


        // 写入日志数据
        $this->db_data->beginTransaction();
        try {
            $parameter['create_time'] = date('Y-m-d H:i:s');
            $parameter['status'] = 'complete';
            $this->db_data->insert('logs_purchase', $parameter);
            $this->db_data->commit();
        } catch (Exception $e) {
            $this->db_data->rollBack();
            return ['code' => 1, 'msg' => 'insert into logs purchase failed'];
        }


        return ['code' => 0, 'msg' => 'success'];
    }

}

