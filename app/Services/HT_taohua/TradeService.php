<?php
namespace Xt\Rpc\Services\HT_taohua;

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
        $coin = $product['coin'];
        $price = $product['price'];
        $prop = null;

        // 检查促销
        $sql = "SELECT type,lowest,coin,prop FROM products_cfg WHERE  product_id=? AND lowest<=? AND ('$dateTimeNow' BETWEEN start_time AND  end_time)";
        $bind[] = $parameter['amount'];
        $cfg = $this->db_data->fetchAll($sql, $bind);
        $total_gold = 0;
        if ($cfg) {
            $total_gold = $coin;
            foreach ($cfg as $k => $v) {
                switch ($v['type']) {
                    case 'promo' :                        //促销
                        $coin += $v['coin'];
                        break;
                    case 'first_purchase' :               //首冲
                        if (!$this->tradeModel->checkFirstPurchase($parameter['user_id'], $parameter['product_id'])) {
                            $coin += $v['coin'];
                        }
                        break;
                }
            }
        }

        //月卡
        if ($product['custom'] == 'card_month') {
            $month_card = 2592000;
        }

        //super_vip
        if ($product['custom'] == 'super_vip') {
            $super_vip = 1;
        }


        //开始事物
        $game_connect = $this->gameDb($zone);
        $game_connect->beginTransaction();
        $game_trade_format = substr($parameter['transaction'], -18); // 兼容游戏订单格式
        try {
            $month_card = empty($month_card) ? 0 : $month_card;
            $super_vip = empty($super_vip) ? 0 : $super_vip;
            if ($total_gold != 0) {
                $sql = "INSERT INTO gold_order (role_id, gold, total_gold, month_card ,super_vip , real_money, billing_id, log_time) VALUES ('$user_id', '$coin', '$total_gold', '$month_card','$super_vip' , '$price','$game_trade_format', '$logTime')";
            }
            else {
                $sql = "INSERT INTO gold_order (role_id, gold, total_gold, month_card ,super_vip , real_money, billing_id, log_time) VALUES ('$user_id', '$coin', '$coin', '$month_card','$super_vip' , '$price','$game_trade_format', '$logTime')";
            }
            $game_connect->executeUpdate($sql);
            $game_connect->commit();
        } catch (Exception $e) {
            $game_connect->rollBack();
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

