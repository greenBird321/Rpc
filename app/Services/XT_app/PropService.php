<?php

/**
 * 道具相关
 * Class PropService
 */
namespace Xt\Rpc\Services\XT_app;


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
                'coin'   => '货币',
                'exp'    => '经验',
                'attach' => '道具',
                'mail'   => '邮件',
            ]
        ];
    }


    public function coin($parameter)
    {
        if (empty($parameter['user_id']) || empty($parameter['amount'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
    }


    public function exp($parameter)
    {
        if (empty($parameter['user_id']) || empty($parameter['amount'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
    }


    public function attach($parameter)
    {
        if (empty($parameter['user_id']) || empty($parameter['attach'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
    }


    public function mail($parameter)
    {
        if (empty($parameter['user_id']) || empty($parameter['msg'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
    }

}