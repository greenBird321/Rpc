<?php

/**
 * 统计相关
 * Class StatService
 */

namespace Xt\Rpc\Services\HT_ynls;


class StatsService extends \Xt\Rpc\Services\XT_app\StatsService
{
    /**
     * http://v3-rpc.com/HT_ynls/zh_CN/stats/useronline?server_id=1&start=2019-09-25&end=2019-10-07
     * @param $parameter
     * @return array
     */
    // 统计在线时长
    public function userOnline($parameter)
    {
        $arr1 = [2, 4, "color" => "red"];
        $arr2 = ["a", "b", "color" => "green", "shape" => "trapezoid", 4];
        $result = array_merge($arr1, $arr2);
        dump($result);
        echo "----------------" . PHP_EOL;
        dump($arr1 + $arr2);
        echo "----------------" . PHP_EOL;
        dump(array_merge_recursive($arr1, $arr2));
        exit;
        if (!empty($parameter['start']) && !empty($parameter['end'])) {
            $start = $parameter['start'];
            $end = $parameter['end'];
        } else {
            return ['code' => 1, 'msg' => 'failed'];
        }
        $date = [];
        for ($i = strtotime($start); $i <= strtotime($end); $i += 86400) {
            $date[] = [
                'start_time' => date("Y-m-d", $i) . ' 00:00:00',
                'end_time' => date("Y-m-d", $i) . ' 23:59:59'
            ];
        }
        /**
         * user_id: role_id
         * day_idx: 当天开始时间
         * online_time: 用户今天在线多少秒
         * update_time： 用户最近一次下线时间
         */
        $db = $this->gameDb($parameter['serverId']);
        foreach ($date as $value) {
            $sql = "SELECT * FROM t_user_onlinetime WHERE day_idx BETWEEN '{$value['start_time']}' AND  '{$value['end_time']}'";
            $key = explode(' ', $value['start_time'])[0];
            $data[$key] = $db->fetchAll($sql);
        }
        //$sql = "SELECT * FROM t_user_onlinetime WHERE day_idx BETWEEN '{$start}' AND  '{$end}'";
//        $data = $db->fetchAll($sql);
//        foreach ($data as $key => $value) {
//           $userName = $this->getRoleName($value['user_id'], $db);
//           $data[$key]['userName'] = $userName;
//        }
        // 导出表格 下次测试后会删除
        $fileName = 'ynls_' . $start . '-' . $end;
        //$sumOnline = array_values($sumOnline['online']);
        foreach ($data as $key => $value) {
            $arr[] = [
                $value['user_id'],
                $value['userName'],
                $value['online_time'],
                $value['day_idx'],
            ];
        }
        $this->daochu_excel($arr, ['user_id', '角色名称', '在线时长/秒', '日期'], $fileName);
        return $data;
    }

    /**
     * 查询角色名称
     * @param $roleId
     */
    public function getRoleName($roleId, $gameDB)
    {
        $sql = "SELECT name FROM t_game_user_global WHERE user_id = {$roleId}";
        $res = $gameDB->prepare($sql);
        $res->execute();
        $name = $res->fetchColumn(0);
        return $name;
    }

    // 快速排序算法
    public function quickSort(array $array)
    {
        if (count($array) <= 1) {
            return $array;
        }

        $key = $array[0];

        $left_arr = [];

        $right_arr = [];

        for ($i = 1; $i < count($array); $i++) {
            // 把数组分为两部分
            if ($array[$i] <= $key) {
                // 小于等于key的分到一个数组中
                $left_arr[] = $array[$i];
            } else {
                // 大于key的分到另外一个数组中
                $right_arr[] = $array[$i];
            }
        }
        // 使用递归 一直处理左右两个数组
        $left_arr = $this->quickSort($left_arr);
        $right_arr = $this->quickSort($right_arr);

        return array_merge($left_arr, array($key), $right_arr);
    }

    /**
     * @param array $data 要导出的数据
     * @param array $title excel表格的表头
     * @param string $filename 文件名
     */

    public function daochu_excel($data = array(), $title = array(), $filename = '报表')
    {//导出excel表格

        //处理中文文件名

        ob_end_clean();

        Header('content-Type:application/vnd.ms-excel;charset=utf-8');


        header("Content-Disposition:attachment;filename=export_data.xls");

        //处理中文文件名

        $ua = $_SERVER["HTTP_USER_AGENT"];


        $encoded_filename = urlencode($filename);

        $encoded_filename = str_replace("+", "%20", $encoded_filename);

        if (preg_match("/MSIE/", $ua) || preg_match("/LCTE/", $ua) || $ua == 'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko') {

            header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xls"');

        } else {

            header('Content-Disposition: attachment; filename="' . $filename . '.xls"');

        }

        header("Content-type:application/vnd.ms-excel");


        $html = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>

            <html xmlns='http://www.w3.org/1999/xhtml'>

            <meta http-equiv='Content-type' content='text/html;charset=UTF-8' />

            <head>

     

            <title>" . $filename . "</title>

            <style>

            td{

                text-align:center;

                font-size:12px;

                font-family:Arial, Helvetica, sans-serif;

                border:#1C7A80 1px solid;

                color:#152122;

                width:auto;

            }

            table,tr{

                border-style:none;

            }

            .title{

                background:#7DDCF0;

                color:#FFFFFF;

                font-weight:bold;

            }

            </style>

            </head>

            <body>

            <table width='100%' border='1'>

              <tr>";

        foreach ($title as $k => $v) {

            $html .= " <td class='title' style='text-align:center;'>" . $v . "</td>";

        }


        $html .= "</tr>";


        foreach ($data as $key => $value) {

            $html .= "<tr>";

            foreach ($value as $aa) {

                $html .= "<td>" . $aa . "</td>";

            }


            $html .= "</tr>";


        }

        $html .= "</table></body></html>";
        echo $html;

        exit;

    }

}