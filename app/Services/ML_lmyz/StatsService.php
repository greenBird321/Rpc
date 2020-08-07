<?php

/**
 * 统计相关
 * Class StatService
 */
namespace Xt\Rpc\Services\ML_lmyz;

class StatsService extends \Xt\Rpc\Services\XT_app\StatsService
{
    public function statsLost($paramter)
    {
        $start = strtotime($paramter['start']);
        $end = strtotime($paramter['end']);
        $lostDay = $paramter['lostDay'];

        $sql = "SELECT
        COUNT( RoleID ) activity_user,
        AreaLastStage,
        SUM( `Money` ) money,
        COUNT(
        nullif( `Money`, '' )) chargeCount 
    FROM
        BasicRes 
    WHERE
        CreateTime BETWEEN $start
        AND $end 
        AND AreaLastStage != ''
    GROUP BY
        AreaLastStage";
        $result[$paramter['zone']] = $this->gameDb($paramter['zone'])->fetchAll($sql);

        if (!empty($result[$paramter['zone']])) {
            foreach ($result as $key => $value) {
                foreach ($value as $k => $v) {
                    $lostUser[$key][$v['AreaLastStage']] = $v;
                    $lostUser[$key][$v['AreaLastStage']]['total_user'] = 0;
                    if ($lostUser[$key][$v['AreaLastStage']]['money'] == null) {
                        $lostUser[$key][$v['AreaLastStage']]['money'] = 0;
                    }
                    
                }
                ksort($lostUser[$key]);
            }
            
            // 获得最大关卡数
            foreach($lostUser as $key => $value) {
               foreach ($value as $k => $v) {
                    $maxKey = 0;
                    if ($k > $maxKey) {
                        $maxKey = $k;
                    }
               }
            }
    
            // 数据补全
            foreach ($lostUser as $key => $value) {
                for ($i = 1; $i <= $maxKey; $i++) {
                    if (!isset($value[$i])) {
                        $lostUser[$key][$i] = [
                            'total_user' => 0,
                            'activity_user' => 0,
                            'AreaLastStage' => $i,
                            'LoginTime' => 0,
                            'money' => 0,
                            'chargeCount' => 0
                        ];
                    }
                }
                ksort($lostUser[$key]);
            }
    
            foreach ($lostUser as $key => $value) {
                $tmp = 0;
                for ($i = count($value); $i > 1; $i--) {
                    $tmp += $value[$i]['activity_user'];
                    $lostUser[$key][$i - 1]['total_user'] += $tmp;
                }
            }
    
            // 计算流失用户
            $lostSql = "SELECT
            RoleID,
            AreaLastStage,
            LoginTime 
        FROM
            BasicRes 
        WHERE
            AreaLastStage != '' 
            AND CreateTime BETWEEN $start 
            AND $end";
            $lost[$paramter['zone']] = $this->gameDb($paramter['zone'])->fetchAll($lostSql);
            
            $lostCount = [];
            foreach ($lost as $key => $value) {
                foreach ($value as $k => $v) {
                    // 流失用户
                    if (time() - (int)$v['LoginTime'] >= $lostDay * 86400) {
                        if (empty($lostCount[$v['AreaLastStage']])) {
                            $lostCount[$v['AreaLastStage']] = 1;
                        } else {
                            $lostCount[$v['AreaLastStage']] += 1;
                        }
                    }
                }
            }
    
            foreach ($lostUser as $key => $value) {
                foreach ($value as $k => $v) {
                    foreach ($lostCount as $i => $j) {
                        if ($i == $k) {
                            $lostUser[$key][$k]['lostCount'] = $j;
                        } 
                    }
                    if (!isset($lostUser[$key][$k]['lostCount'])) {
                        $lostUser[$key][$k]['lostCount'] = 0;
                    }
                    if ($lostUser[$key][$k]['total_user'] != 0) {
                        $lostUser[$key][$k]['lostRate'] = round($lostUser[$key][$k]['lostCount'] / $lostUser[$key][$k]['total_user'], 2) * 100 . '%';
                    } else {
                        $lostUser[$key][$k]['lostRate'] = '0%';
                    }
                    
                }
            }
    
            return [
                'code' => 0,
                'msg' => 'success',
                'data' => $lostUser
            ];
        } else {
            return [
                'code' => 0,
                'msg' => 'success',
                'data' => []
            ];
        }
    }


    public function statsTimeQuery($paramter) 
    {
        if (empty($paramter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
        
        $start = $paramter['start'];
        $end = $paramter['end'];

        $server = $paramter['zone'];
        if (strpos($server, ',')) {
            $server = explode(',', $server);
        } else {
            $server = [$server];
        }

        //  查找新增用户
        $addSql = "SELECT
        RoleID add_user,
        FROM_UNIXTIME( CreateTime, '%Y-%m-%d' ) date 
    FROM
       `LoginLog` 
    WHERE
        RoleName = '' 
        AND `CreateTime` BETWEEN $start 
        AND $end 
        ";

        // 查找活跃用户
        $activitySql = "SELECT
        DISTINCT (RoleID) activity_user,
        FROM_UNIXTIME( LoginTime, '%Y-%m-%d' ) date 
        FROM
        `LoginLog` 
        WHERE
        `LoginTime` BETWEEN $start 
        AND $end 
        AND RoleName != ''";

        foreach ($server as $value) {
            $add_result[$value] = $this->gameDb($value . '_log')->fetchAll($addSql);
            $act_result[$value] = $this->gameDb($value . '_log')->fetchAll($activitySql);
        }

        // 整理数据结构
        foreach ($add_result as $key => $value) {
            $new_add[$key] = $this->array_column_index($value, 'date');
            $new_add_info[$key] = $this->array_column_index($value, 'add_user');
        }

        // 整理数据结构
        foreach ($act_result as $key => $value) {
            $new_act[$key] = $this->array_column_index($value, 'date');
            $new_act_info[$key] = $this->array_column_index($value, 'activity_user');
        }

        // 新增用户
        $add_user_count = [];
        foreach ($new_add as $key => $value) {
            foreach ($value as $k => $v) {
                $add_user_count[$key][$k] = count($v); 
            }
        }

        // 活跃用户
        $act_user_count = [];
        foreach ($new_act as $key => $value) {
            foreach ($value as $k => $v) {
                $act_user_count[$key][$k] = count($v); 
            }
        }

        // 新增+活跃的roleID
        for ($i = 1; $i <= count($new_add_info); $i++) {
            $total_user_info[$i] = $new_add_info[$i] + $new_act_info[$i];
        }

        $total_roleId = [];
        // 数据整理
        foreach ($total_user_info as $key => $value) {
            foreach ($value as $k => $v) {
                $total_roleId[] = $k;
            }
        }

        // 查询新增+活跃的充值总额和充值人数
        $money = [];
        if (!empty($total_roleId)) {
            $sqlStr = implode(',', $total_roleId);
            $moneySql = "SELECT
            SUM( Money ) money,
            COUNT( RoleID ) chargeCount ,
            FROM_UNIXTIME( ChargeTime, '%Y-%m-%d' ) date 
        FROM
            `ChargeLog` 
        WHERE
            RoleID IN ( {$sqlStr} ) 
        GROUP BY
            FROM_UNIXTIME(
                ChargeTime,
            '%Y-%m-%d' 
            )";
            foreach ($server as $v) {
                $money[$v] =$this->gameDb($v.'_log')->fetchAll($moneySql);
            }
        }
       
        // 数据合并
        $tmp = [];
        for ($i = 1; $i <= count($add_user_count); $i++) {
            foreach($add_user_count[$i] as $key => $value) {
                foreach ($act_user_count[$i] as $k => $v) {
                    if ($key == $k) {
                        $tmp[$k][] = [
                            'add_user_count' => $value,
                            'act_user_count' => $v + $value,
                            'serverId' => $i,
                        ];
                    }
                }
            }
        }

        // 合并充值金额以及充值人数
        if (!$this->checkArrayIsNull($money) && !empty($tmp)) {
            foreach ($money as $key => $value) {
                foreach ($value as $k => $v) {
                    foreach ($tmp as $i => $j) {
                        foreach ($j as $a => $b ) {
                             if ($b['serverId'] == $key && $i == $v['date']) {
                                 $tmp[$i][$a]['money'] = $v['money'];
                                 $tmp[$i][$a]['chargeCount'] = $v['chargeCount'];
                             }
                        }
                    }
                } 
            }

            // 数据补全
            foreach ($tmp as $key => $value) {
                foreach ($value as $k => $v) {
                    if (!isset($v['money'])) {
                        $tmp[$key][$k]['money'] = 0;
                    } 
                    if (!isset($v['chargeCount'])) {
                        $tmp[$key][$k]['chargeCount'] = 0;
                    }

                    if ($tmp[$key][$k]['act_user_count'] != 0) {
                        $tmp[$key][$k]['chargeRate'] = round($tmp[$key][$k]['chargeCount'] / $tmp[$key][$k]['act_user_count'], 2)* 100 . '%';
                        $tmp[$key][$k]['arpu'] = round($tmp[$key][$k]['money'] / $tmp[$key][$k]['act_user_count'], 2);
                    } else {
                        $tmp[$key][$k]['chargeRate'] = '0%';
                        $tmp[$key][$k]['arpu'] = 0;
                    }
                    
                    if ($tmp[$key][$k]['chargeCount'] != 0) {
                        $tmp[$key][$k]['arppu'] = round($tmp[$key][$k]['money'] / $tmp[$key][$k]['chargeCount']);
                    } else {
                        $tmp[$key][$k]['arppu'] = 0;
                    }
                }
            }
        } else {
            // 没有数据的情况下，充值人数以及充值金额填充0
            foreach ($tmp as $key => $value) {
                foreach ($value as $k => $v) {
                    $tmp[$key][$k]['chargeCount'] = 0;
                    $tmp[$key][$k]['money'] = 0;
                    $tmp[$key][$k]['chargeRate'] = 0;
                    $tmp[$key][$k]['arpu'] = 0;
                    $tmp[$key][$k]['arppu'] = 0;
                }
            }
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $tmp
        ];
    }


    public function statsContrast($paramter)
    {
        if (empty($paramter['zone'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $stats_start = $paramter['start'];
        $stats_end = $paramter['end'];

        $server = $paramter['zone'];
        if (strpos($server, ',')) {
            $server = explode(',', $server);
        } else {
            $server = [$server];
        }

        //  查找新增用户
        $addSql = "SELECT
        RoleID add_user,
        FROM_UNIXTIME( CreateTime, '%Y-%m-%d' ) date 
    FROM
       `LoginLog` 
    WHERE
        RoleName = '' 
        AND `CreateTime` BETWEEN $stats_start 
        AND $stats_end 
        ";
        
        // 查找活跃用户
        $activitySql = "SELECT
        DISTINCT (RoleID) activity_user,
        FROM_UNIXTIME( LoginTime, '%Y-%m-%d' ) date 
        FROM
        `LoginLog` 
        WHERE
        `LoginTime` BETWEEN $stats_start 
        AND $stats_end 
        AND RoleName != ''";

        foreach ($server as $value) {
            $add_result[$value] = $this->gameDb($value . '_log')->fetchAll($addSql);
            $act_result[$value] = $this->gameDb($value . '_log')->fetchAll($activitySql);
        }

        // 整理数据结构
        foreach ($add_result as $key => $value) {
            $new_add[$key] = $this->array_column_index($value, 'date');
        }

        // 整理数据结构
        foreach ($act_result as $key => $value) {
            $new_act[$key] = $this->array_column_index($value, 'date');
        }

        $temp_add = [];
        $temp_act = [];
        foreach ($new_add as $key => $value) {
            foreach ($value as $k => $v) {
                foreach ($v as $i => $j) {
                    $temp_add[$key][$k][] = $j['add_user'];
                }
            }
        }

        foreach ($new_act as $key => $value) {
            foreach ($value as $k => $v) {
                foreach ($v as $i => $j) {
                    $temp_act[$key][$k][] = $j['activity_user'];
                }
            }
        }

        foreach ($temp_add as $key => $value ){
            foreach ($value as $k => $v) {
                $start = strtotime($k.' 00:00:00');
                $end = strtotime($k.' 23:59:59');
                $date_roleId = implode(',', $v);
                $addsql = "SELECT
                COUNT( RoleID ) addRoleChargeCount,
                IFNULL(SUM( Money ), 0) money
            FROM
                `ChargeLog` 
            WHERE
                `RoleID` IN (
                    $date_roleId
                ) 
                AND `ChargeTime` >=  $start 
                AND `ChargeTime` <=  $end";

                foreach ($server as $server_id) {
                    $add_temp[$server_id][$k] = $this->gameDb($server_id.'_log')->fetchAssoc($addsql);
                }
            }
        }

        foreach ($temp_act as $key => $value) {
            foreach ($value as $k => $v) {
                $start = strtotime($k.' 00:00:00');
                $end = strtotime($k.' 23:59:59');
                $date_roleId = implode(',', $v);
                $oldsql = "SELECT
                COUNT( RoleID ) oldRoleChargeCount,
                IFNULL(SUM( Money ), 0) money
            FROM
                `ChargeLog` 
            WHERE
                `RoleID` IN (
                    $date_roleId
                ) 
                AND `ChargeTime` >=  $start 
                AND `ChargeTime` <=  $end";

                foreach ($server as $server_id) {
                    $act_temp[$server_id][$k] = $this->gameDb($server_id.'_log')->fetchAssoc($oldsql);
                }
            }
        }
        
        $final = [];
        // 数据合并(addUserCount + actUserCount)
        foreach ($new_act as $key => $value) {
            foreach ($value as $k => $v) {
                $final[$key][$k] = [
                    'addUserCount' => count($new_add[$key][$k]),
                    'oldUserCount' => count($v),
                    'addRoleChargeCount' => $add_temp[$key][$k]['addRoleChargeCount'],
                    'addRoleMoney' => $add_temp[$key][$k]['money'],
                    'addRoleRote' => empty(count($new_add[$key][$k])) ? '0%' : round($add_temp[$key][$k]['addRoleChargeCount'] / count($new_add[$key][$k]), 2) * 100 . '%',
                    'addRolearpu' => empty(count($new_add[$key][$k])) ? '0%' : round($add_temp[$key][$k]['money'] / count($new_add[$key][$k]), 2),
                    'addRolearppu' => empty($add_temp[$key][$k]['addRoleChargeCount']) ? '0' : $add_temp[$key][$k]['money'] / $add_temp[$key][$k]['addRoleChargeCount'],
                    'oldRoleChargeCount' => $act_temp[$key][$k]['oldRoleChargeCount'],
                    'oldRoleMoney' => $act_temp[$key][$k]['money'],
                    'oldRoleRote' => empty(count($new_act[$key][$k])) ? '0%' : round($act_temp[$key][$k]['oldRoleChargeCount'] / count($new_act[$key][$k]) , 2) * 100 . '%',
                    'oldRolearpu' => empty(count($new_act[$key][$k])) ? '0%' : round($act_temp[$key][$k]['money'] / count($new_act[$key][$k]), 2),
                    'oldRolearppu' => empty($act_temp[$key][$k]['oldRoleChargeCount']) ? '0' : $act_temp[$key][$k]['money'] / $act_temp[$key][$k]['oldRoleChargeCount'],
                ];
            }
        }

        
        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $final
        ];
    }

    /**
     * 检测二维数据，key对应的value是否为空
     */
    public function checkArrayIsNull($array)
    {
        $result = true;
        foreach ($array as $key => $value) {
            if (!empty($value)) {
                $result = false;
            }
        }
        return $result;
    }

     /**
     * des: 将二维数组的某个列作为键, 会将相同键的数据合并成一个新数组
     * @param $arr [type|array] 数据(二维数组)
     * @param $index [type|string] 作为键的某列
     * @return array
     */
    public function array_column_index($arr, $index = '')
    {
        $new = array();
        $used = array();

        // 必须确定列名是存在的
        if (!empty($arr) && !empty($index)) {
            foreach ($arr as $key => $value) {
                // 非数组则不执行
                if (!is_array($value)) {
                    $new = $arr;
                    break;
                }

                // 检查该键是否已经使用, 使用过则需要合并相同键内的数据
                if (!empty($used[$value[$index]])) {
                    $used[$value[$index]][] = $value;
                    $new[$value[$index]] = $used[$value[$index]];
                } else {
                    $used[$value[$index]][] = $value;       // 标记是否使用
                    $new[$value[$index]][] = $value;
                }
            }
        } else {
            $new = $arr;
        }

        return $new;
    }
}