<?php
/**
 * 活动相关
 * Class ActivityService
 */
namespace Xt\Rpc\Services\ML_lmyz;


class ActivityService extends \Xt\Rpc\Services\XT_app\ActivityService
{
    public function test($parameter)
    {
        return [
            'code' => 0,
            'msg' => $parameter['msg']
        ];
    }
}