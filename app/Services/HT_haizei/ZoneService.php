<?php

/**
 * 服务器管理
 * Class ZoneService
 */
namespace Xt\Rpc\Services\HT_haizei;


use Xt\Rpc\Core\Service;
use Exception;

class ZoneService extends Service
{

    private $_status = [
        'on'      => 0,
        'off'     => 64,
        'sandbox' => 128,
    ];

    private $_tag = [
        'hot'       => 0, // 普通
        'usual'     => 0, // 普通
        'recommend' => 2, // 推荐
        'new'       => 3, // 新开推荐
    ];


    public function lists($parameter)
    {
        $sql = "SELECT * FROM game_server WHERE 1=1";
        $lists = $this->gameDb('zone_list')->fetchAll($sql);

        $dict_status = array_flip($this->_status);
        $dict_tag = array_flip($this->_tag);
        $zone_list = [];
        foreach ($lists as $key => $value) {
            $zone_list[$key]['id'] = $value['group_id'];
            $zone_list[$key]['name'] = $value['server_name'];
            $zone_list[$key]['host'] = $value['domain'];
            $zone_list[$key]['port'] = $value['port'];

            $tag = $value['flags'];
            if (isset($dict_status[$tag])) {
                $zone_list[$key]['status'] = $dict_status[$tag];
            }
            else {
                $zone_list[$key]['status'] = 'on';
            }
            if (isset($dict_tag[$tag])) {
                $zone_list[$key]['tag'] = $dict_tag[$tag];
            }
            else {
                $zone_list[$key]['tag'] = 'usual';
            }
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $zone_list
        ];
    }


    public function item($parameter)
    {
        $id = intval($parameter['id']);
        $sql = "SELECT * FROM game_server WHERE group_id=$id";
        $response = $this->gameDb('zone_list')->fetchAssoc($sql);
        if (!$response) {
            return ['code' => 1, 'msg' => 'no data'];
        }
        $result = [
            'id'   => $response['group_id'],
            'name' => $response['server_name'],
            'host' => $response['domain'],
            'port' => $response['port'],
        ];

        $dict_status = array_flip($this->_status);
        $dict_tag = array_flip($this->_tag);
        $tag = $response['flags'];
        if (isset($dict_status[$tag])) {
            $result['status'] = $dict_status[$tag];
        }
        else {
            $result['status'] = 'on';
        }
        if (isset($dict_tag[$tag])) {
            $result['tag'] = $dict_tag[$tag];
        }
        else {
            $result['tag'] = 'usual';
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $result
        ];
    }


    public function create($parameter)
    {
        if (empty($parameter['id']) || empty($parameter['name']) || empty($parameter['host'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        $parameter['id'] = intval($parameter['id']);
        $parameter['port'] = intval($parameter['port']);

        if ($parameter['status'] != 'on') {
            $tag = $this->_status[$parameter['status']];
        }
        else {
            $tag = $this->_tag[$parameter['tag']];
        }

        $data = [
            'group_id'    => $parameter['id'],
            'server_name' => $parameter['name'],
            'domain'      => $parameter['host'],
            'port'        => $parameter['port'],
            'flags'       => $tag,
            'secret_key'  => $parameter['custom'],
        ];
        try {
            $this->gameDb('zone_list')->insert('game_server', $data);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }


    public function modify($parameter)
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        $parameter['id'] = intval($parameter['id']);
        $parameter['port'] = intval($parameter['port']);

        if ($parameter['status'] != 'on') {
            $tag = $this->_status[$parameter['status']];
        }
        else {
            $tag = $this->_tag[$parameter['tag']];
        }

        $data = [
            'group_id'    => $parameter['id'],
            'server_name' => $parameter['name'],
            'domain'      => $parameter['host'],
            'port'        => $parameter['port'],
            'flags'       => $tag,
            'secret_key'  => $parameter['custom'],
        ];
        try {
            $this->gameDb('zone_list')->update('game_server', $data, ['group_id' => $parameter['id']]);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }


    public function remove($parameter)
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        try {
            $this->gameDb('zone_list')->delete('game_server', ['group_id' => $parameter['id']]);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }

}