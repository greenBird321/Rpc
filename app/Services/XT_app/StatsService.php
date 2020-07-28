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

    /**
     * 玩家关卡通过
     */
    public function userPassLevel($parameter)
    {
        $start    = $parameter['start'];
        $end      = $parameter['end'];
        $serverId = $parameter['serverId'];

        try {
            // 查询玩家总数
            $total_sql = "SELECT
	COUNT(
	DISTINCT ( RoleID )) totalUser
FROM
	AreaStage 
WHERE
	`Status` IN (
		0,
	1) AND PassTime BETWEEN {$start} AND {$end}
" ;
            $total = $this->gameDb($serverId)->fetchAssoc($total_sql);
            // 查询所有关卡
            $levelInfo_sql = "SELECT
	count( 1 ) passUser,
	aa.AreaID 
FROM
	(
	SELECT
		count( 1 ),
		RoleID,
		AreaID 
	FROM
		AreaStage 
	WHERE
		`Status` = 1 
		AND PassTime BETWEEN {$start} 
		AND {$end} 
	GROUP BY
		RoleID,
		AreaID 
	) aa 
GROUP BY
	aa.AreaID";
            $level_info = $this->gameDb($serverId)->fetchAll($levelInfo_sql);

            $server_sql = "SELECT `Name` serverName FROM ServerRegion WHERE ServerId = {$serverId}";
            $serverName = $this->gameDb('zone_list')->fetchAssoc($server_sql);
            // 查询关卡通过数
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        // 如果没有新增玩家
        if (empty($total['totalUser']) || empty($level_info)) {
            // 查询最高关卡并且补充数据
            $nodata_sql = "	SELECT MAX(AreaID) areaId FROM AreaStage";
            $levelId = $this->gameDb($parameter['serverId'])->fetchAssoc($nodata_sql);

            for($i = 1; $i <= $levelId['areaId']; $i++) {
                $levelInfo[] = [
                    'serverName' => $serverName['serverName'],
                    'total' => intval($total['totalUser']),
                    'levelId' => $i,
                    'userCount' => 0,
                    'userMix' => 0,
                ];
            }

            return [
                'code' => 0,
                'msg' => 'success',
                'data' => $levelInfo,
            ];

        }

        // 处理数据正常
        foreach ($level_info as $key => $value) {
            $userMix = sprintf("%.4f",$value['passUser'] / $total['totalUser']) * 100 . '%';
            $data[] = [
                'serverName' => $serverName['serverName'],
                'total' => $total['totalUser'],
                'levelId' => $value['AreaID'],
                'userCount' => $value['passUser'],
                'userMix' => $userMix
            ];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $data
        ];
    }
}