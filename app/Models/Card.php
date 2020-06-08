<?php

namespace Xt\Rpc\Models;


use Xt\Rpc\Core\Model;
use Exception;

class Card extends Model
{


    public function lists($parameter = [])
    {
        $parameter = array_filter($parameter, function ($item) {
            if ($item === null) {
                return false;
            }
            return true;
        });

        $sql = "SELECT * FROM card_topic WHERE 1=1";
        $bind = [];

        // 条件
        if (!empty($parameter['type'])) {
            $sql .= ' AND type=?';
            $bind[] = $parameter['type'];
        }
        if (!empty($parameter['title'])) {
            $sql .= " AND title LIKE '%{$parameter['title']}%'";
        }

        // 分页
        $page = !empty($parameter['page']) ? $parameter['page'] : 1;
        $size = !empty($parameter['size']) ? $parameter['size'] : 200;
        $offset = ($page - 1) * $size;

        // SQL
        $sqlCount = str_replace('*', 'COUNT(1) count', $sql);
        $sqlData = str_replace(
            '*',
            'id,type,data,status,title,limit_times,expired_in,start_time',
            $sql
        );
        $sqlData .= " ORDER BY id DESC LIMIT $offset,$size";


        // count
        $count = $this->db_data->fetchColumn($sqlCount, $bind);
        if (!$count) {
            return ['count' => 0];
        }

        // result
        $results = $this->db_data->fetchAll($sqlData, $bind);
        if (!$results) {
            throw new Exception('no data');
        }

        return [
            'count' => $count,
            'data' => $results
        ];
    }


    public function item($parameter = [])
    {
        $sql = "SELECT id,type,data,title,intro,limit_times,expired_in, code_limit_times,start_time FROM card_topic WHERE id=?";
        $bind[] = $parameter['id'];

        $result = $this->db_data->fetchAssoc($sql, $bind);
        return ['data' => $result];
    }


    public function create($parameter = [])
    {
        // 不能为0或者负数
        $count = $parameter['count'];

        unset($parameter['count']);
        if ($count > 100000) {
            $count = 100000;
        }

        if ($parameter['code_limit_times'] > 100000) {
            $parameter['code_limit_times'] = 100000;
        }

        // 存储卡片内容
        $this->db_data->insert('card_topic', $parameter);
        $topic = $this->db_data->lastInsertId();

        // 生成卡号
        $utils = new Utils();
        for ($i = 0; $i < $count; $i++) {
            $card = $utils->genString(true);
            $this->db_data->insert('card_code', ['code' => $card, 'topic_id' => $topic, 'status' => 1]);
        }

        return true;
    }


    public function modify($parameter = [])
    {
        if (empty($parameter['id'])) {
            return false;
        }
        $id = intval($parameter['id']);
        unset($parameter['id']);

        $parameter = array_filter($parameter, function ($item) {
            if ($item === null) {
                return false;
            }
            return true;
        });

        return $this->db_data->update('card_topic', $parameter, array('id' => $id));
    }


    public function remove($parameter = [])
    {
        $id = intval($parameter['id']);
        $this->db_data->delete('card_topic', array('id' => $id));
        $this->db_data->delete('card_code', array('topic_id' => $id));
        return true;
    }


    public function search($parameter = [])
    {
        $sql = "SELECT * FROM `logs_card` WHERE user_id={$parameter['role_id']} AND item_id={$parameter['topic_id']}  ORDER BY `create_time` DESC";
        $data = $this->db_data->fetchAll($sql);
        return ['data' => $data];
    }

    public function download($id)
    {
        $sql = "SELECT code,status FROM `card_code` WHERE topic_id={$id}";
        $data = $this->db_data->fetchAll($sql);
        $result = [];
        foreach ($data as $v) {
            $result[] = array_values($v);
        }
        return $result;
    }
}