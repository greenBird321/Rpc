<?php
/**
 * Created by PhpStorm.
 * User: lihe
 * Date: 2020/6/22
 * Time: 8:14 PM
 */

namespace Xt\Rpc\Services\ML_lmyz;

class GameService extends \Xt\Rpc\Services\XT_app\GameService
{
    public function getConsumeList($parameter) {
        if (empty($parameter['serverId'])) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        try {
            $sql = "SELECT
	SUM( ItemNum ) diamonds,
	`Action` actionId 
FROM
	ItemLog 
WHERE
	GetItem = 0 
	AND ActionTime BETWEEN {$parameter['start']}
	AND {$parameter['end']}
GROUP BY
	actionId 
ORDER BY
	diamonds DESC";
            $result = $this->gameDb($parameter['serverId']. '_log')->fetchAll($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
         }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $result
        ];
    }

    public function shopRanking($parameter)
    {
        if (empty($parameter['serverId'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $start = strtotime($parameter['start']);
        $end   = strtotime($parameter['end']);

        try {
            $sql = "
SELECT
	ActionTime,
	Action,
	ItemId,
	ItemNum buy_propNum
FROM
	ItemLog 
WHERE
	GetItem = 1 
	AND Action IN ( 100001, 100002, 100005, 100011, 100012 ) 
	AND ActionTime BETWEEN {$start} 
	AND {$end} 

";

            $result = $this->gameDb($parameter['serverId'].'_log')->fetchAll($sql);
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $result
        ];
    }
}