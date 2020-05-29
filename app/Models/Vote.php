<?php

namespace Xt\Rpc\Models;


use Xt\Rpc\Core\Model;

class Vote extends Model
{

    public function lists_topic($parameter = [])
    {
        $sql = "SELECT * FROM vote_topic WHERE 1=1";
        $bind = [];

        // 条件
        if (!empty($parameter['title'])) {
            $sql .= " AND title LIKE '%{$parameter['title']}%'";
        }
        if (!empty($parameter['start_time'])) {
            $sql .= ' AND start_time>=?';
            $bind[] = $parameter['start_time'];
        }
        if (!empty($parameter['end_time'])) {
            $sql .= ' AND end_time<=?';
            $bind[] = $parameter['end_time'];
        }


        // 分页
        $page = !empty($parameter['page']) ? $parameter['page'] : 1;
        $size = !empty($parameter['size']) ? $parameter['size'] : 200;
        $offset = ($page - 1) * $size;

        // SQL
        $sqlCount = str_replace('*', 'COUNT(1) count', $sql);
        $sqlData = str_replace(
            '*',
            'id,status,title,intro,img,start_time,end_time',
            $sql
        );
        $sqlData .= " ORDER BY start_time LIMIT $offset,$size";


        // count
        $count = $this->db_data->fetchColumn($sqlCount, $bind);
        if (!$count) {
            return ['count' => 0];
        }

        // result
        $results = $this->db_data->fetchAll($sqlData, $bind);

        return [
            'count' => $count,
            'data'  => $results
        ];
    }


    public function item_topic($parameter = [])
    {
        $sql = "SELECT id,status,title,intro,img,start_time,end_time FROM vote_topic WHERE id=?";
        $bind[] = $parameter['id'];

        $result = $this->db_data->fetchAssoc($sql, $bind);
        return ['data' => $result];
    }


    public function create_topic($parameter = [])
    {
        $parameter['status'] = ($parameter['status'] !== '') ? intval($parameter['status']) : 1;
        return $this->db_data->insert('vote_topic', $parameter);
    }


    public function modify_topic($parameter = [])
    {
        if (empty($parameter['id'])) {
            return false;
        }
        $id = intval($parameter['id']);
        unset($parameter['id']);
        $parameter['status'] = ($parameter['status'] !== '') ? intval($parameter['status']) : 1;

        return $this->db_data->update('vote_topic', $parameter, array('id' => $id));
    }


    public function remove_topic($parameter = [])
    {
        $id = intval($parameter['id']);
        $this->db_data->delete('vote_topic', array('id' => $id));
        $this->db_data->delete('vote_options', array('group_id' => $id));
        return true;
    }


    public function lists_option($parameter = [])
    {
        $sql = "SELECT id,subject,answer,option_1,option_2,option_3,option_4 FROM vote_options WHERE group_id=?";
        $results = $this->db_data->fetchAll($sql, [$parameter['group_id']]);
        return ['data' => $results];
    }


    public function item_option($parameter = [])
    {
        $sql = "SELECT id,group_id,subject,answer,option_1,option_2,option_3,option_4 FROM vote_options WHERE id=?";
        $bind[] = $parameter['id'];

        $result = $this->db_data->fetchAssoc($sql, $bind);
        return ['data' => $result];
    }


    public function create_option($parameter = [])
    {
        $parameter['answer'] = ($parameter['answer'] !== '') ? intval($parameter['answer']) : 0;
        return $this->db_data->insert('vote_options', $parameter);
    }


    public function modify_option($parameter = [])
    {
        $parameter['answer'] = ($parameter['answer'] !== '') ? intval($parameter['answer']) : 0;
        $id = intval($parameter['id']);
        unset($parameter['id']);
        return $this->db_data->update('vote_options', $parameter, array('id' => $id));
    }


    public function remove_option($parameter = [])
    {
        $id = intval($parameter['id']);
        return $this->db_data->delete('vote_options', array('id' => $id));
    }

}