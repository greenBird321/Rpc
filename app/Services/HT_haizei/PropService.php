<?php

/**
 * 道具相关
 * Class PropService
 */
namespace Xt\Rpc\Services\HT_haizei;


use Exception;
use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Utils;

class PropService extends Service
{

    private $utilsModel;


    public function __construct($di)
    {
        parent::__construct($di);
        $this->utilsModel = new Utils();
    }


    public function attribute($parameter)
    {
        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => [
                //'coin'   => '货币',
                //'exp'    => '经验',
                //'attach' => '道具',
                'mail'         => '邮件',
                'attachServer' => '全服发奖',
            ]
        ];
    }


    public function coin($parameter)
    {
        if (empty($parameter['zone']) || empty($parameter['user_id']) || empty($parameter['amount'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        $zone = $parameter['zone'];
        $user_id = $parameter['user_id'];
        $amount = intval($parameter['amount']);


        $appDateTime = $this->utilsModel->switchTimeZone($this->di['db_cfg']['setting']['timezone'],
            $this->di['config']['setting']['timezone']);


        // 开始处理
        $conn = $this->gameDb($zone);
        $conn->beginTransaction();
        try {
            // 更新角色
            $sql = "UPDATE role SET gold=gold + $amount WHERE role_id=?";
            $conn->executeUpdate($sql, [$user_id]);

            // 货币日志
            $sql = "INSERT INTO gold_log
            SET role_id='$user_id',
            gold='$amount',
            role_level=(SELECT level FROM role WHERE role_id='$user_id'),
            vip_level=0,
            reason='0',
            order_id='0',
            log_time='$appDateTime'";
            $conn->executeUpdate($sql);

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            $this->di['logger']->error('prop-coin error', $parameter);
            return ['code' => 1, 'msg' => 'failed'];
        }

        if (!empty($parameter['msg'])) {
            $this->_mailTo($zone, $user_id, $parameter['msg']);
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function exp($parameter)
    {
        if (empty($parameter['zone']) || empty($parameter['user_id']) || empty($parameter['amount'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        $zone = $parameter['zone'];
        $user_id = $parameter['user_id'];
        $amount = intval($parameter['amount']);

        try {
            $sql = "UPDATE role SET exp=exp + $amount WHERE role_id=?";
            $this->gameDb($zone)->executeUpdate($sql, [$user_id]);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        if (!empty($parameter['msg'])) {
            $this->_mailTo($zone, $user_id, $parameter['msg']);
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function attach($parameter)
    {
        if (empty($parameter['zone']) || empty($parameter['user_id']) || empty($parameter['attach'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        $zone = $parameter['zone'];
        $user_id = $parameter['user_id'];
        $msg = empty($parameter['msg']) ? '' : $parameter['msg'];

        $appDateTime = $this->utilsModel->switchTimeZone($this->di['db_cfg']['setting']['timezone'],
            $this->di['config']['setting']['timezone']);


        // 开始处理
        try {
            $conn = $this->gameDb($zone);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        $attach_list = explode(',', $parameter['attach']);
        if (!$attach_list) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        foreach ($attach_list as $attach) {
            if (strpos($attach, '*')) {
                list($att, $num) = explode('*', $attach);
            }
            else {
                $att = $attach;
                $num = 1;
            }
            for ($i = 0; $i < $num; $i++) {
                $conn->beginTransaction();
                try {
                    $sql = "INSERT INTO mail (`role_id`, `other_role`, `type`, `unread`, `gift`, `content`, `sent_time`) VALUES ('{$user_id}', '0', '1', '1', '{$att}', '{$msg}', '{$appDateTime}')";
                    $conn->executeUpdate($sql);

                    $lastInsertId = $conn->lastInsertId();
                    $sql = "INSERT INTO maillog(mid,roleid,datetime) VALUES($lastInsertId,$user_id,'$appDateTime')";
                    $conn->executeUpdate($sql);

                    $conn->commit();
                } catch (Exception $e) {
                    $conn->rollBack();
                    $this->di['logger']->error('prop-attach error', $parameter);
                }
            }
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function mail($parameter)
    {
        if (empty($parameter['zone']) || empty($parameter['user_id']) || empty($parameter['msg'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        $zone = $parameter['zone'];
        $user_id = $parameter['user_id'];
        $msg = $parameter['msg'];

        if (!$this->_mailTo($zone, $user_id, $msg)) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    private function _mailTo($zone = 0, $user_id = 0, $msg = '')
    {
        $appDateTime = $this->utilsModel->switchTimeZone($this->di['db_cfg']['setting']['timezone'],
            $this->di['config']['setting']['timezone']);

        $conn = $this->gameDb($zone);
        try {
            $sql = "INSERT INTO mail SET role_id='$user_id', other_role=0, type=1, unread=1, gift='999999999', content='$msg', sent_time='$appDateTime'";
            $conn->executeUpdate($sql);

            $lastInsertId = $conn->lastInsertId();
            $sql = "INSERT INTO maillog(mid,roleid,datetime) VALUES($lastInsertId,$user_id,'$appDateTime')";
            $conn->executeUpdate($sql);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

}