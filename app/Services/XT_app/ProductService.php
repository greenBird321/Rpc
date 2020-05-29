<?php

/**
 * 产品接口
 * Class ProductService
 */
namespace Xt\Rpc\Services\XT_app;


use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Product;
use Exception;

class ProductService extends Service
{

    private $productModel;


    public function __construct($di)
    {
        parent::__construct($di);
        $this->productModel = new Product();
    }


    public function lists($parameter = [])
    {
        try {
            $data = $this->productModel->lists($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }


    public function item($parameter = [])
    {
        if (empty($parameter['product_id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $data = $this->productModel->item($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }


    public function create($parameter = [])
    {
        if (empty($parameter['product_id'])
            || empty($parameter['gateway'])
            || empty($parameter['price'])
            || empty($parameter['currency'])
            || empty($parameter['coin'])
        ) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->productModel->create($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function modify($parameter = [])
    {
        if (empty($parameter['product_id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->productModel->modify($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function remove($parameter = [])
    {
        if (empty($parameter['product_id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->productModel->remove($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function create_option($parameter = [])
    {
        if (empty($parameter['product_id'])
            || empty($parameter['type'])
            || empty($parameter['start_time'])
            || empty($parameter['end_time'])
        ) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->productModel->create_option($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function modify_option($parameter = [])
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->productModel->modify_option($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function remove_option($parameter = [])
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->productModel->remove_option($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        return ['code' => 0, 'msg' => 'success'];
    }

}