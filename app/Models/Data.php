<?php

namespace Xt\Rpc\Models;


use Xt\Rpc\Core\Model;

class Data extends Model
{


    public function get_users_login($start = '', $end = '')
    {
        $s = substr($start, 0, 7);
        $e = substr($end, 0, 7);
        $month = str_replace('-', '', $s);

        // 自动转换为游戏配置的时区，按游戏时区计算登录天数
        $appTimeZone = $this->db_cfg['setting']['timezone'];
        $timezone = (new \DateTime('now', new \DateTimeZone($appTimeZone)))->format('P');
        $utc = '+00:00';

        if ($s == $e) {
            $sql = "SELECT CONCAT(t.zone,'-',t.user_id) user_id, COUNT(1) times FROM (
SELECT zone, user_id, SUBSTR(CONVERT_TZ(create_time, '$utc', '$timezone'),1,10) days FROM users_login_{$month} WHERE app_id={$this->route['game_id']} AND create_time BETWEEN '$start' AND '$end' GROUP BY zone,user_id,days
) t GROUP BY t.zone,t.user_id";
        }
        else {
            // TODO :: 最多跨1个月
            $month_end = str_replace('-', '', $e);
            $sql = "SELECT CONCAT(t.zone,'-',t.user_id) user_id, COUNT(DISTINCT days) times FROM (
SELECT zone, user_id, SUBSTR(CONVERT_TZ(create_time, '$utc', '$timezone'),1,10) days FROM users_login_{$month} WHERE app_id={$this->route['game_id']} AND create_time BETWEEN '$start' AND '$end' GROUP BY zone,user_id,days
UNION
SELECT zone, user_id, SUBSTR(CONVERT_TZ(create_time, '$utc', '$timezone'),1,10) days FROM users_login_{$month_end} WHERE app_id={$this->route['game_id']} AND create_time BETWEEN '$start' AND '$end' GROUP BY zone,user_id,days
) t GROUP BY t.zone,t.user_id";
        }

        $data = $this->db_logs->fetchAll($sql);
        if (!$data) {
            return [];
        }

        $result = [];
        foreach ($data as $value) {
            $result[$value['user_id']] = $value['times'];
        }
        return $result;
    }

}