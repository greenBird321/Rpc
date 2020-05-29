<?php

/**
 * 服务器管理
 * Class ZoneService
 */

namespace Xt\Rpc\Services\HT_taohua3;


use Xt\Rpc\Core\Service;
use Exception;

class ZoneService extends Service
{
    private $server_status = [
        'sandbox'=>0,
        'on'=>1,
    ];

    private $full_mode = [
        'No'=>0,
        'Yes'=>1
    ];

    private $into_server = [
        'EveryOne'=>0,
        'Gm'=>1,
        'NoOne'=>2
    ];

    private $server_visible = [
        'No'=>0,
        'Yes'=>1,
    ];

    private $color_mode = [
        'Green'=>0,
        'Red'=>1,
    ];

    public function lists()
    {
        $sql = "SELECT * FROM game_server";
        $lists = $this->gameDb('zone_list')->fetchAll($sql);
        $dict_status = array_flip($this->server_status);
        $dict_full_mode = array_flip($this->full_mode);
        $dict_visible = array_flip($this->server_visible);

        foreach ($lists as &$server) {
            $server['id'] = $server['server_id'];
            $server['name'] = $server['server_name'];
            $server['host'] = $server['client_domain'];
            $server['port'] = $server['client_port'];
            $server['open_mode'] = $dict_status[$server['open_mode']];
            $server['full_mode'] = $dict_full_mode[$server['full_mode']];
            $server['visible_mode'] = $dict_visible[$server['visible_mode']];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $lists
        ];
    }

    public function item($parameter)
    {
        $id = intval($parameter['id']);
        $sql = "SELECT * FROM game_server WHERE server_id=$id";
        $response = $this->gameDb('zone_list')->fetchAssoc($sql);
        if (!$response) {
            return ['code' => 1, 'msg' => 'no data'];
        }
        $dict_status = array_flip($this->server_status);
        $dict_full_mode = array_flip($this->full_mode);
        $dict_visible = array_flip($this->server_visible);
        $dict_color_mode = array_flip($this->color_mode);
        $result = [
            'id' => $response['server_id'],
            'name' => $response['server_name'],
            'host' => $response['client_domain'],
            'port' => $response['client_port'],
            'login_domain' => $response['login_domain'],
            'login_port' => $response['client_port'],
            'open_mode' => $dict_status[$response['open_mode']],
            'color_mode' => $dict_color_mode[$response['color_mode']],
            'full_mode' => $dict_full_mode[$response['full_mode']],
            'visible_mode' => $dict_visible[$response['visible_mode']],
            'role_count' => $response['role_count'],
            'update_time' => $response['update_time'],
        ];

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $result
        ];
    }

    public function create($parameter)
    {
        if (empty($parameter['id']) || empty($parameter['name'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
//        file_put_contents('test',json_encode($parameter));
        $dist_open_mode = 0;
        $dist_color_mode = 0;
        $dist_full_mode = 0;
        $dist_visible_mode = 0;
        if(isset($parameter['open_mode']))
        {
            $dist_open_mode  = $this->server_status[$parameter['open_mode']];
        }
        if(isset($parameter['color_mode']))
        {
            $dist_color_mode  = $this->color_mode[$parameter['color_mode']];
        }
        if(isset($parameter['full_mode']))
        {
            $dist_full_mode = $this->full_mode[$parameter['full_mode']];
        }
        if(isset($parameter['visible_mode']))
        {
            $dist_visible_mode  = $this->server_visible[$parameter['visible_mode']];
        }

        $parameter['id'] = intval($parameter['id']);
        $parameter['port'] = intval($parameter['port']);
        $data = [
            'client_domain' => $parameter['host'],
            'client_port' => $parameter['port'],
            'login_domain' => '',
            'login_port' => 0,
            'server_name' => $parameter['name'],
            'group_name' => 0,
            'open_mode' => $dist_open_mode,
            'color_mode' => $dist_color_mode,
            'full_mode' => $dist_full_mode,
            'visible_mode' => $dist_visible_mode,
            'update_time' => date('Y-m-d H:i:s'),
        ];
        file_put_contents('test',json_encode($data));
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
//        file_put_contents('test',json_encode($parameter));
        $parameter['id'] = intval($parameter['id']);
        $parameter['port'] = intval($parameter['port']);

        $data = [
            'client_domain' => $parameter['host'],
            'client_port' => $parameter['port'],
            'server_name' => $parameter['name'],
            'open_mode' => $this->server_status[$parameter['open_mode']],
            'color_mode' => $this->color_mode[$parameter['color_mode']],
            'full_mode' => $this->full_mode[$parameter['full_mode']],
            'visible_mode' => $this->server_visible[$parameter['visible_mode']],
            'update_time' => date('Y-m-d H:i:s'),
        ];

        try {
            $this->gameDb('zone_list')->update('game_server', $data, ['server_id' => $parameter['id']]);
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
            $this->gameDb('zone_list')->delete('game_server', ['server_id' => $parameter['id']]);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }
}