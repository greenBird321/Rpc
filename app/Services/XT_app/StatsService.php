<?php

/**
 * 全服统计相关
 * Class StatService
 */

namespace Xt\Rpc\Services\XT_app;


use Exception;
use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Stats;

class StatsService extends Service
{

    protected $statsModel;

    public function __construct($di)
    {
        parent::__construct($di);
        $this->statsModel = new Stats();
    }


    public function realTime($parameter)
    {
        if (!empty($parameter['date'])) {
            $parameter['date'] = date('Y-m-d', strtotime($parameter['date']));
        } else {
            $parameter['date'] = date('Y-m-d');
        }
        $data = $this->statsModel->getRealTime($parameter);

        if (!$data) {
            return ['code' => 1, 'msg' => 'no data'];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $data
        ];
    }

    /**
     * 统计用户在线时长 (按天)
     * @param $parameter
     */
    public function userOnline($parameter)
    {
    }

    /**
     * 区服等级分布
     */
    public function zone_level()
    {
    }


    /**
     * 货币持有量查询
     */
    public function get_coin()
    {
    }


    /**
     * 道具持有量查询
     */
    public function get_prop()
    {
    }

}