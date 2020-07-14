<?php
namespace Xt\Rpc\Models;

use Xt\Rpc\Core\Model;

class Game extends Model
{
    // todo 如果新加rpc的话，需要修改app_id
    // 由于是按照服务器id 和 channel 去查询，只能去trade库去查询
    public function top($parameter, $count = 20)
    {
        if (empty($parameter['start']) || empty($parameter['end'])) {
            $time_sql = "";
        } else {
            $time_sql = " BETWEEN '{$parameter['start']}' AND  '{$parameter['end']}'";
        }

        if (!empty($parameter['zone'] && empty($parameter['channel']))) {
            // 通过 服务器id 查询
            $count = strlen($parameter['zone']);
            $role_count = $count + 2;
            $sql = "SELECT
	SUBSTR(custom, 1, {$count}) as serverId,
	SUBSTR( custom, {$role_count}) as roleId,
	SUM( amount ) amount,
	channel
FROM
	transactions 
WHERE
	app_id = 1051020 
	AND `status` = 'complete'
	AND SUBSTR(custom, 1, {$count}) = '{$parameter['zone']}'
	AND `complete_time` $time_sql
GROUP BY
	SUBSTR( custom, {$role_count}) 
ORDER BY
	amount DESC 
	LIMIT {$count}";
            return $this->db_trade->fetchAll($sql);
        } elseif (!empty($parameter['channel'] && empty($parameter['zone']))) {
            // 通过渠道查询
            $sql = "SELECT
            custom,
	SUM( amount ) amount,
	channel
FROM
	transactions 
WHERE
	app_id = 1051020 
	AND `status` = 'complete'
	AND `channel` = '{$parameter['channel']}'
	AND `complete_time` $time_sql
GROUP BY
	custom
ORDER BY
	amount DESC 
	LIMIT {$count}";
            $result = $this->db_trade->fetchAll($sql);
            foreach ($result as $key => $value) {
                list($result[$key]['serverId'], $result[$key]['roleId']) = explode('-', $value['custom']);
                unset($result[$key]['custom']);
            }
            return $result;
        } else {
            // 通过 服务器id和渠道一起查询
            $count = strlen($parameter['zone']);
            $role_count = $count + 2;
            $sql = "SELECT
	SUBSTR(custom, 1, {$count}) as serverId,
	SUBSTR( custom, {$role_count}) as roleId,
	SUM( amount ) amount,
	channel
FROM
	transactions 
WHERE
	app_id = 1051020 
	AND `status` = 'complete'
	AND `channel` = '{$parameter['channel']}'
	AND SUBSTR(custom, 1, {$count}) = '{$parameter['zone']}'
	AND `complete_time` $time_sql
GROUP BY
	SUBSTR( custom, {$role_count}) 
ORDER BY
	amount DESC 
	LIMIT {$count}";
            return $this->db_trade->fetchAll($sql);
        }
    }

    // todo 如果新加rpc的话，需要修改app_id
    public function distribution($parameter)
    {
        if (empty($parameter['start']) || empty($parameter['end'])) {
            $time_sql = "";
        } else {
            $time_sql = " BETWEEN '{$parameter['start']}' AND  '{$parameter['end']}'";
        }
        $count = strlen($parameter['zone']);
        $sql = "SELECT
    ext levelId,
	COUNT( `transaction` ) totalCount,
	SUM( amount ) totalMoeny
FROM
	transactions 
WHERE
	app_id = 1051020 
	AND `status` = 'complete' 
	AND `is_first` = 1 
	AND SUBSTR( custom, 1, {$count}) = {$parameter['zone']}
	AND complete_time {$time_sql}
GROUP BY
	ext";
        return $this->db_trade->fetchAll($sql);
    }

    // todo 如果新加rpc的话，需要修改app_id
    public function rechargeDistribution($parameter)
    {
        $count = strlen($parameter['zone']);
        $sql = "SELECT
	COUNT( DISTINCT custom ) rechargePlayerCount,
	SUM( amount ) rechargeAmount
FROM
	transactions 
WHERE
	SUBSTR( custom, 1, {$count} ) = {$parameter['zone']}
	AND app_id = 1051020 
	AND `status` = 'complete'";
        return $this->db_trade->fetchAssoc($sql);
    }

    public function rechargeDistributionInfo($parameter)
    {
        $count = strlen($parameter['zone']);
        if (empty($parameter['start']) || empty($parameter['end'])) {
            $time_sql = "";
        } else {
            $time_sql = " BETWEEN '{$parameter['start']}' AND  '{$parameter['end']}'";
        }
        $sql = "SELECT
	COUNT( DISTINCT custom ) rechargePlayerCount,
	SUM( amount ) rechargeAmount
FROM
	transactions 
WHERE
	SUBSTR( custom, 1, {$count} ) = {$parameter['zone']}
	AND app_id = 1051020 
	AND `status` = 'complete'
	AND `complete_time` {$time_sql}";
        return $this->db_trade->fetchAssoc($sql);
    }
}