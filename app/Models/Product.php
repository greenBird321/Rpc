<?php

namespace Xt\Rpc\Models;


use Xt\Rpc\Core\Model;
use Exception;

class Product extends Model
{

    public function lists($parameter)
    {
        $sql = "SELECT * FROM products WHERE 1=1";
        $bind = [];

        if (!empty($parameter['gateway'])) {
            $sql .= ' AND gateway=?';
            $bind[] = $parameter['gateway'];
        }

        // 分页
        $page = !empty($parameter['page']) ? $parameter['page'] : 1;
        $size = !empty($parameter['size']) ? $parameter['size'] : 9999;
        $offset = ($page - 1) * $size;

        // SQL
        $sqlCount = str_replace('*', 'COUNT(1) count', $sql);
        $sqlData = str_replace(
            '*',
            'product_id,gateway,price,currency,coin,custom,status,sort,name,remark,image,package',
            $sql
        );
        $sqlData .= " ORDER BY sort DESC LIMIT $offset,$size";


        // count
        $count = $this->db_data->fetchColumn($sqlCount, $bind);
        if (!$count) {
            throw new Exception('no data');
        }

        // result
        $results = $this->db_data->fetchAll($sqlData, $bind);

        return [
            'count' => $count,
            'data'  => $results
        ];
    }


    public function item($parameter)
    {
        $sql = "SELECT product_id,gateway,price,currency,coin,custom,status,sort,name,remark,image,package FROM products WHERE product_id=?";
        $bind[] = $parameter['product_id'];

        $result = $this->db_data->fetchAssoc($sql, $bind);
        if (!$result) {
            throw new Exception('no data');
        }

        $sql = "SELECT id,product_id,type,lowest,coin,prop,start_time,end_time FROM products_cfg WHERE product_id=?";
        $result['more'] = $this->db_data->fetchAll($sql, $bind);
        return [
            'data' => $result
        ];
    }


    public function create($parameter)
    {
        $parameter['sort'] = !empty($parameter['sort']) ? intval($parameter['sort']) : 0;
        $parameter['status'] = !empty($parameter['status']) ? intval($parameter['status']) : 0;
        $parameter['coin'] = !empty($parameter['coin']) ? intval($parameter['coin']) : 0;

        return $this->db_data->insert('products', $parameter);
    }


    public function modify($parameter)
    {
        $parameter = array_filter($parameter, function ($item) {
            if ($item === null) {
                return false;
            }
            return true;
        });

        $product_id = trim($parameter['old_product_id']);
        unset($parameter['old_product_id']);

        $parameter['sort'] = !empty($parameter['sort']) ? intval($parameter['sort']) : 0;
        $parameter['status'] = !empty($parameter['status']) ? intval($parameter['status']) : 0;
        $parameter['coin'] = !empty($parameter['coin']) ? intval($parameter['coin']) : 0;

        return $this->db_data->update('products', $parameter, array('product_id' => $product_id));
    }


    public function remove($parameter)
    {
        $product_id = trim($parameter['product_id']);
        $this->db_data->delete('products', array('product_id' => $product_id));
        $this->db_data->delete('products_cfg', array('product_id' => $product_id));
        return true;
    }


    public function create_option($parameter)
    {
        $parameter['lowest'] = !empty($parameter['lowest']) ? intval($parameter['lowest']) : 0;
        $parameter['coin'] = !empty($parameter['coin']) ? intval($parameter['coin']) : 0;
        return $this->db_data->insert('products_cfg', $parameter);
    }


    public function modify_option($parameter)
    {
        $parameter = array_filter($parameter, function ($item) {
            if ($item === null) {
                return false;
            }
            return true;
        });

        $parameter['lowest'] = !empty($parameter['lowest']) ? intval($parameter['lowest']) : 0;
        $parameter['coin'] = !empty($parameter['coin']) ? intval($parameter['coin']) : 0;

        $id = intval($parameter['id']);
        return $this->db_data->update('products_cfg', $parameter, array('id' => $id));
    }


    public function remove_option($parameter)
    {
        return $this->db_data->delete('products_cfg', array('id' => $parameter['id']));
    }

}