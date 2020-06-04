<?php

/**
 * 道具相关
 * Class PropService
 */
namespace Xt\Rpc\Services\ML_lmyz;


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

        return ['code' => 0, 'msg' => 'success'];
    }

}