<?php

/**
 * 数据接口
 * Class DataService
 */
namespace Xt\Rpc\Services\XT_app;


use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Activity;
use Xt\Rpc\Models\Data;

class DataService extends Service
{

    private $dataModel;


    public function __construct($di)
    {
        parent::__construct($di);
        $this->dataModel = new Data();
    }


    public function get_users_prepay($parameter)
    {
        empty($parameter['act_id']) ? $parameter['act_id'] = 0 : null;
        empty($parameter['zone']) ? $parameter['zone'] = '' : null;
        empty($parameter['min']) ? $parameter['min'] = 0 : null;
        empty($parameter['max']) ? $parameter['max'] = 99999999 : null;

        $activityModel = new Activity();
        $activity = $activityModel->item(['id' => $parameter['act_id']]);
        if (!$activity) {
            return ['code' => 1, 'msg' => 'no activity'];
        }
        $start = $activity['start_time'];
        $end = $activity['end_time'];

        $sql = "SELECT user_id, SUM(amount) amount FROM `logs_purchase` WHERE create_time BETWEEN '$start' AND '$end' GROUP BY user_id";
        $result = $this->db_data->fetchAll($sql);
        if (!$result) {
            return ['code' => 1, 'msg' => 'no data'];
        }

        $output = [];
        foreach ($result as $value) {
            $output[$value['user_id']] = $value['amount'];
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $output
        ];
    }


    public function get_users_spend($parameter)
    {
        empty($parameter['act_id']) ? $parameter['act_id'] = 0 : null;
        empty($parameter['zone']) ? $parameter['zone'] = '' : null;
        empty($parameter['min']) ? $parameter['min'] = 0 : null;
        empty($parameter['max']) ? $parameter['max'] = 99999999 : null;

        $key = $this->di['route']['game_id'] . ':act:' . $parameter['act_id'];
        $this->di['redis']->select(3);
        $data = $this->di['redis']->ZREVRANGEBYSCORE($key, $parameter['max'], $parameter['min'],
            ['withscores' => true]);

        if (!$data) {
            return ['code' => 1, 'msg' => 'no data'];
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $data
        ];
    }


    public function get_users_login($parameter)
    {
        empty($parameter['act_id']) ? $parameter['act_id'] = 0 : null;
        empty($parameter['zone']) ? $parameter['zone'] = '' : null;
        empty($parameter['min']) ? $parameter['min'] = 0 : null;
        empty($parameter['max']) ? $parameter['max'] = 999 : null;

        $activityModel = new Activity();
        $activity = $activityModel->item(['id' => $parameter['act_id']]);
        if (!$activity) {
            return ['code' => 1, 'msg' => 'no activity'];
        }
        $start = $activity['start_time'];
        $end = $activity['end_time'];
        $data = $this->dataModel->get_users_login($start, $end);

        if (!$data) {
            return ['code' => 1, 'msg' => 'no data'];
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $data
        ];
    }

}