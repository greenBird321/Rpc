<?php

/**
 * 充值相关
 * Class Trade
 */
namespace Xt\Rpc\Services\HT_haizei;


use Exception;
use Xt\Rpc\Models\Trade;
use Xt\Rpc\Models\Utils;
use Xt\Rpc\Core\Service;

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
        // 暂不检查签名
        unset($parameter['timestamp'], $parameter['sign']);
        if (!strpos($parameter['user_id'], '-')) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        list($zone, $user_id) = explode('-', $parameter['user_id']);


        // 当前RPC系统时间
        $dateTimeNow = date('Y-m-d H:i:s');
        // 当前游戏时间
        $appDateTime = $this->utilsModel->switchTimeZone($this->di['db_cfg']['setting']['timezone'],
            $this->di['config']['setting']['timezone']);


        // 检查订单
        $sql = "SELECT id FROM logs_purchase WHERE transaction=?";
        if ($this->db_data->fetchAssoc($sql, [$parameter['transaction']])) {
            $this->di['logger']->error('purchase error - transaction exist', $parameter);
            return false;
        }


        // 检查产品
        $sql = "SELECT product_id,name,gateway,price,currency,coin,custom FROM products WHERE status=1 AND product_id=?";
        $bind[] = $parameter['product_id'];
        $product = $this->db_data->fetchAssoc($sql, $bind);
        if (!$product) {
            $this->di['logger']->error('purchase error - no product', $parameter);
            return false;
        }
        $coin = $product['coin'];
        $prop = null;


        // 检查促销
        $sql = "SELECT type,lowest,coin,prop FROM products_cfg WHERE product_id=? AND lowest<=? AND ('$dateTimeNow' BETWEEN start_time AND end_time)";
        $bind[] = $parameter['amount'];
        $cfg = $this->db_data->fetchAll($sql, $bind);
        if ($cfg) {
            foreach ($cfg as $k => $v) {
                switch ($v['type']) {
                    case 'promo':               // 促销
                        $coin += $v['coin'];
                        break;
                    case 'first_purchase':      // 首次充值
                        if (!$this->tradeModel->checkFirstPurchase($parameter['user_id'], $parameter['product_id'])) {
                            $coin += $v['coin'];
                        }
                        break;
                }
            }
        }


        // 开始处理
        $conn = $this->gameDb($zone);


        // 重置月卡 card_month, card_week, vip
        if ($product['custom'] == 'card_month') {
            $sql = "SELECT role_id FROM role WHERE role_id=$user_id AND month_card < NOW()";
            $res = $conn->fetchAssoc($sql);
            if ($res) {
                $sql = "UPDATE role SET month_card=NOW() WHERE role_id=$user_id";
                if (!$conn->executeUpdate($sql)) {
                    $this->di['logger']->error('purchase error - reset month card', $parameter);
                }
            }
        }

        $game_trade_format = substr($parameter['transaction'], -12, 9); // 兼容游戏订单格式
        $conn->beginTransaction();
        try {
            // 日志
            $sql = "INSERT INTO gold_log
            SET role_id='$user_id',
            gold='$coin',
            total_gold='{$product['coin']}',
            role_level=(SELECT level FROM role WHERE role_id='$user_id'),
            vip_level=0,
            reason='0',
            order_id='{$game_trade_format}',
            log_time='$appDateTime'";
            $conn->executeUpdate($sql);

            // 更新玩家
            $sql = "UPDATE role SET gold=gold + $coin, total_gold=total_gold + {$product['coin']}";
            if ($product['custom'] == 'card_month') {
                $sql .= ", month_card=DATE_ADD(`month_card`,INTERVAL " . 31 . " DAY)";
            }
            elseif ($product['custom'] == 'vip') {
                $sql .= ", super_vip=1";
            }
            $sql .= " WHERE role_id=$user_id";
            $conn->executeUpdate($sql);

            // 邮件 (type:1普通消息, 2充值消息)
            $sql = "INSERT INTO mail SET role_id=$user_id, other_role=0, type=2, unread=1, gift='$coin', content='', sent_time='$appDateTime'";
            $conn->executeUpdate($sql);

            // 邮件日志
            $lastInsertId = $conn->lastInsertId();
            $sql = "INSERT INTO maillog(mid,roleid,datetime) VALUES($lastInsertId,$user_id,'$appDateTime')";
            $conn->executeUpdate($sql);

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            $this->di['logger']->error('purchase error', $parameter);
            return ['code' => 1, 'msg' => 'failed'];
        }


        // 事务
        $this->db_data->beginTransaction();
        try {
            // 写入日志数据
            $parameter['create_time'] = date('Y-m-d H:i:s');
            $parameter['status'] = 'complete';
            $this->db_data->insert('logs_purchase', $parameter);
            $this->db_data->commit();
        } catch (Exception $e) {
            $this->db_data->rollBack();
            return ['code' => 1, 'msg' => 'failed'];
        }

        return ['code' => 0, 'msg' => 'success'];
    }

}