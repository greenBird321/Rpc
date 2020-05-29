<?php

/**
 * 服务器管理
 * Class ZoneService
 */
namespace Xt\Rpc\Services\XT_app;


use Xt\Rpc\Core\Service;

class ZoneService extends Service
{

    public function lists($parameter)
    {
    }


    public function create($parameter)
    {
    }


    public function modify($parameter)
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
    }


    public function remove($parameter)
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
    }

}