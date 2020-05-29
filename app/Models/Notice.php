<?php

namespace Xt\Rpc\Models;


use Xt\Rpc\Core\Model;
use Exception;

class Notice extends Model
{


    public function lists($parameter = [])
    {
        $sql = "SELECT * FROM notice WHERE 1=1";
        $bind = [];

        // 条件
        if (!empty($parameter['start_time'])) {
            $sql .= ' AND start_time>=?';
            $bind[] = $parameter['start_time'];
        }
        if (!empty($parameter['end_time'])) {
            $sql .= ' AND end_time<=?';
            $bind[] = $parameter['end_time'];
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
            'id,zone,channel,status,sort,title,content,img,start_time,end_time',
            $sql
        );
        $sqlData .= " ORDER BY sort DESC, end_time DESC LIMIT $offset,$size";


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
            'data'  => $results
        ];
    }


    public function item($parameter = [])
    {
        $sql = "SELECT id,zone,channel,status,sort,title,content,img,start_time,end_time FROM notice WHERE id=?";
        $bind[] = $parameter['id'];

        $result = $this->db_data->fetchAssoc($sql, $bind);
        if (!$result) {
            throw new Exception('no data');
        }

        return [
            'data' => $result
        ];
    }


    public function create($parameter = [])
    {
        $parameter['status'] = ($parameter['status'] !== '') ? intval($parameter['status']) : 1;
        $parameter['sort'] = ($parameter['sort'] !== '') ? intval($parameter['sort']) : 0;

        return $this->db_data->insert('notice', $parameter);
    }


    public function modify($parameter = [])
    {
        if (empty($parameter['id'])) {
            return false;
        }
        $id = intval($parameter['id']);
        unset($parameter['id']);

        $parameter['sort'] = ($parameter['sort'] !== '') ? intval($parameter['sort']) : 0;
        $parameter['status'] = ($parameter['status'] !== '') ? intval($parameter['status']) : 1;

        return $this->db_data->update('notice', $parameter, array('id' => $id));
    }


    public function remove($parameter = [])
    {
        $id = intval($parameter['id']);
        $this->db_data->delete('notice', array('id' => $id));
        return true;
    }
}