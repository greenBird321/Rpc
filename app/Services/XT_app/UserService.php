<?php

/**
 * 用户相关
 * Class UserService
 */
namespace Xt\Rpc\Services\XT_app;


use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Utils;

class UserService extends Service
{

    public function __construct($di)
    {
        parent::__construct($di);
        $this->utilsModel = new Utils();
    }


    /**
     * 用户信息
     * @param $parameter
     * @return array
     */
    public function profile($parameter)
    {
        if (empty($parameter['id'])) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        if (strpos($parameter['id'], '-')) {
            list($zone, $user_id) = explode('-', $parameter['id']);
        }
        else {
            $zone = 0;
            $user_id = $parameter['id'];
        }
    }

}