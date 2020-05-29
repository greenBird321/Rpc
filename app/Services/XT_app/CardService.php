<?php

/**
 * 礼品卡
 * Class CardService
 */
namespace Xt\Rpc\Services\XT_app;


use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Card;
use Exception;

class CardService extends Service
{

    private $cardModel;

    public function __construct($di)
    {
        parent::__construct($di);
        $this->cardModel = new Card();
    }


    public function lists($parameter = [])
    {
        try {
            $data = $this->cardModel->lists($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }


    public function item($parameter = [])
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $data = $this->cardModel->item($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }


    public function create($parameter = [])
    {
        if (empty($parameter['count'])
            || empty($parameter['type'])
            || empty($parameter['data'])
            || empty($parameter['title'])
            || empty($parameter['expired_in']
            || empty($parameter['code_limit_times']))
        ) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->cardModel->create($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function modify($parameter = [])
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->cardModel->modify($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => $e->getMessage()
            ];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function remove($parameter = [])
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->cardModel->remove($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return ['code' => 0, 'msg' => 'success'];
    }

}