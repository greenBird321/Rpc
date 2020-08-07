<?php

/**
 * 服务器管理
 * Class ZoneService
 */
namespace Xt\Rpc\Services\ML_lmyz;


use Xt\Rpc\Core\Service;
use Exception;

class ZoneService extends Service
{
    // 服务器推荐状态
    private $_flag = [
        0 => 'notrecommend', // 不推荐
        1 => 'default',     // 默认推荐
        3 => 'hot',         // 强烈推荐
    ];

    // 服务器状态
    private $_status = [
        0 => 'usually', // 普通
        1 => 'full',   // 爆满
        2 => 'boom',   // 繁荣
        3 => 'fluent', // 流畅
        4 => 'defend', // 维护
    ];

    // 新开区
    private $_tag = [
        0 => 'old',
        1 => 'new',
    ];

    // 服务器列表
    public function lists($parameter)
    {
        $sql = "SELECT * FROM ServerRegion";
        $response = $this->gameDb('zone_list')->fetchAll($sql);

        if (!$response) {
            return ['code' => 1, 'msg' => 'no data'];
        }

        $list = [];
        foreach ($response as $value) {
            $list[] = [
                'server_id'       => $value['ServerId'],
                'name'            => $value['Name'],
                'host'            => $value['GsIp'],
                'port'            => $value['GsPort'],
                'open_time'       => $value['OpenTime'],
                'merge_server_id' => $value['MergeId'],
                'open_mode'       => $value['Status'],
                'tag'             => $value['NewRegion'],
                'flag'            => $value['Flag'],
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $list
        ];
    }


    public function item($parameter)
    {
        $id = intval($parameter['id']);
        $sql = "SELECT * FROM ServerRegion WHERE ServerId=$id";
        $response = $this->gameDb('zone_list')->fetchAssoc($sql);
        if (!$response) {
            return ['code' => 1, 'msg' => 'no data'];
        }

        $result = [
            'id'   => $response['ServerId'],
            'name' => $response['Name'],
            'host' => $response['GsIp'],
            'port' => $response['GsPort'],
            'open_time' => date('Y-m-d H:i:s', $response['OpenTime']),
            'merge_id' => $response['MergeId'],
            'status' => $response['Status'],
            'is_new' => $response['NewRegion'],
            'flag'  => $response['Flag']
        ];

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $result
        ];
    }


    public function create($parameter)
    {
        if (empty($parameter['name']) || empty($parameter['host'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        $parameter['port'] = intval($parameter['port']);

        $data = [
            'Name'      => $parameter['name'],
            'Flag'      => $parameter['flag'],
            'Status'    => $parameter['open_mode'],
            'NewRegion' => 1,
            'MergeId'   => 0,
            'GsIp'      => $parameter['host'],
            'OpenTime'  => time(),
            'GsPort'    => $parameter['port']
        ];

        try {
            $this->gameDb('zone_list')->insert('ServerRegion', $data);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }


    public function modify($parameter)
    {
        if ($parameter['id'] == null) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        $parameter['id']       = intval($parameter['id']);
        $parameter['port']     = intval($parameter['port']);
        $parameter['merge_id'] = intval($parameter['merge_id']);

        $data = [
            'Name'      => $parameter['name'],
            'Flag'      => $parameter['flag'],
            'Status'    => $parameter['open_mode'],
            'GsIp'      => $parameter['host'],
            'GsPort'    => $parameter['port'],
            'NewRegion' => $parameter['is_new'],
            'MergeId'   => $parameter['merge_id'],
        ];
        try {
            $this->gameDb('zone_list')->update('ServerRegion', $data, ['ServerId' => $parameter['id']]);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }


    public function remove($parameter)
    {
        if ($parameter['id'] == null) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->gameDb('zone_list')->delete('ServerRegion', ['ServerId' => $parameter['id']]);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }

}