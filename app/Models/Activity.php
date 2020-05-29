<?php

namespace Xt\Rpc\Models;


use Xt\Rpc\Core\Model;

class Activity extends Model
{

    public function lists($parameter = [])
    {
        $sql = "SELECT * FROM activity WHERE 1=1";
        $bind = [];

        // 条件
        if (!empty($parameter['type'])) {
            $sql .= ' AND type=?';
            $bind[] = $parameter['type'];
        }
        if (!empty($parameter['start_time'])) {
            $sql .= ' AND start_time>=?';
            $bind[] = $parameter['start_time'];
        }
        if (!empty($parameter['end_time'])) {
            $sql .= ' AND end_time<=?';
            $bind[] = $parameter['end_time'];
        }
        if (!empty($parameter['search'])) {
            $sql .= " AND title LIKE '%{$parameter['search']}%'";
        }

        // 分页
        $page = !empty($parameter['page']) ? $parameter['page'] : 1;
        $size = !empty($parameter['size']) ? $parameter['size'] : 200;
        $offset = ($page - 1) * $size;

        // SQL
        $sqlCount = str_replace('*', 'COUNT(1) count', $sql);
        $sqlData = str_replace(
            '*',
            'id,zone,channel,status,type,visible,title,content,url,img,img_small,custom,sort,start_time,end_time',
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

        return [
            'count' => $count,
            'data'  => $results
        ];
    }


    public function item($parameter = [])
    {
        $sql = "SELECT id,zone,channel,status,type,visible,title,content,url,img,img_small,custom,sort,start_time,end_time FROM activity WHERE id=?";
        $bind[] = $parameter['id'];

        return $this->db_data->fetchAssoc($sql, $bind);
    }


    public function create($parameter = [])
    {
        // int filter
        $parameter['status'] = ($parameter['status'] !== '') ? intval($parameter['status']) : 1;
        $parameter['visible'] = ($parameter['visible'] !== '') ? intval($parameter['visible']) : 1;

        return $this->db_data->insert('activity', $parameter);
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
        $parameter['visible'] = ($parameter['visible'] !== '') ? intval($parameter['visible']) : 1;

        return $this->db_data->update('activity', $parameter, array('id' => $id));
    }


    public function remove($parameter = [])
    {
        $id = intval($parameter['id']);
        $this->db_data->delete('activity', array('id' => $id));
        $this->db_data->delete('activity_cfg', array('item_id' => $id));
        return true;
    }


    public function lists_cfg($parameter = [])
    {
        $sql = "SELECT `id`,`item_id`,`step`,`prop`,`sort`,`title`,`content` FROM `activity_cfg` WHERE `item_id`=? ORDER BY `sort` DESC,`step` ASC";
        $bind[] = $parameter['item_id'];
        return [
            'data' => $this->db_data->fetchAll($sql, $bind)
        ];
    }


    public function item_cfg($parameter = [])
    {
        $sql = "SELECT `id`,`item_id`,`step`,`prop`,`sort`,`title`,`content` FROM `activity_cfg` WHERE `id`=?";
        $bind[] = $parameter['id'];
        return [
            'data' => $this->db_data->fetchAssoc($sql, $bind)
        ];
    }


    public function create_cfg($parameter = [])
    {
        $parameter['item_id'] = ($parameter['item_id'] !== '') ? intval($parameter['item_id']) : 0;
        $parameter['step'] = ($parameter['step'] !== '') ? intval($parameter['step']) : 0;
        $parameter['sort'] = ($parameter['sort'] !== '') ? intval($parameter['sort']) : 0;

        return $this->db_data->insert('activity_cfg', $parameter);
    }


    public function modify_cfg($parameter = [])
    {
        $id = intval($parameter['id']);
        unset($parameter['id']);

        $parameter['item_id'] = ($parameter['item_id'] !== '') ? intval($parameter['item_id']) : 0;
        $parameter['step'] = ($parameter['step'] !== '') ? intval($parameter['step']) : 0;
        $parameter['sort'] = ($parameter['sort'] !== '') ? intval($parameter['sort']) : 0;

        return $this->db_data->update('activity_cfg', $parameter, array('id' => $id));
    }


    public function remove_cfg($parameter = [])
    {
        return $this->db_data->delete('activity_cfg', $parameter, array('id' => intval($parameter['id'])));
    }


    public function logs($parameter = [])
    {
        $sql = "SELECT * FROM logs_activity WHERE 1=1 ";
        $bind = [];

        // 条件
        if (!empty($parameter['item_id'])) {
            $sql .= ' AND item_id=?';
            $bind[] = $parameter['item_id'];
        }
        if (!empty($parameter['cfg_id'])) {
            $sql .= ' AND cfg_id=?';
            $bind[] = $parameter['cfg_id'];
        }
        if (!empty($parameter['zone']) && !empty($parameter['user_id'])) {
            $sql .= ' AND user_id=?';
            $bind[] = $parameter['zone'] . '-' . $parameter['user_id'];
        }
        if (!empty($parameter['start_time'])) {
            $sql .= ' AND create_time>=?';
            $bind[] = $parameter['start_time'];
        }
        if (!empty($parameter['end_time'])) {
            $sql .= ' AND create_time<=?';
            $bind[] = $parameter['end_time'];
        }
        if (!empty($parameter['prop'])) {
            $sql .= " AND prop LIKE '%{$parameter['prop']}%'";
        }

        // 分页
        $page = !empty($parameter['page']) ? $parameter['page'] : 1;
        $size = !empty($parameter['size']) ? $parameter['size'] : 200;
        $offset = ($page - 1) * $size;

        // SQL
        $sqlCount = str_replace('*', 'COUNT(1) count', $sql);
        $sqlData = str_replace('*', 'id,item_id,cfg_id,user_id,prop,create_time', $sql);
        $sqlData .= " ORDER BY id DESC LIMIT $offset,$size";

        // 查询
        $count = $this->db_data->fetchColumn($sqlCount, $bind);
        if (!$count) {
            return ['count' => 0];
        }
        $result = $this->db_data->fetchAll($sqlData, $bind);

        return [
            'count' => $count,
            'data'  => $result
        ];
    }

}