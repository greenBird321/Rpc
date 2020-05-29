<?php

/**
 * 道具相关
 * Class PropService
 */
namespace Xt\Rpc\Services\HT_ynls;


class PropService extends \Xt\Rpc\Services\HT_haizei\PropService
{

    public function coin($parameter)
    {
        return ['code' => 1, 'msg' => 'failed'];
    }

    public function attach($parameter)
    {
        if (empty($parameter['zone']) || empty($parameter['user_id']) || empty($parameter['attach'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        $zone = $parameter['zone'];
        $user_id = $parameter['user_id'];
        $msg = empty($parameter['msg']) ? '' : $parameter['msg'];

//        $appDateTime = $this->utilsModel->switchTimeZone($this->di['db_cfg']['setting']['timezone'],
//            $this->di['config']['setting']['timezone']);
        $appDateTime =  time();

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
                try {
                    $sql = "INSERT INTO mail (`role_id`, `type`, `attachment`, `content`, `sent_time`) VALUES ('{$user_id}', '1', '{$att}', '{$msg}', '{$appDateTime}')";
                    $conn->executeUpdate($sql);
                } catch (Exception $e) {
                    $this->di['logger']->error('prop-attach error', $parameter);
                }
            }
        }

        return ['code' => 0, 'msg' => 'success'];
    }

}