<?php

/**
 * 公告
 * Class NoticeService
 */
namespace Xt\Rpc\Services\XT_app;


use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Notice;
use Exception;

class NoticeService extends Service
{

    private $noticeModel;

    public function __construct($di)
    {
        parent::__construct($di);
        $this->noticeModel = new Notice();
    }


    public function lists($parameter = [])
    {
        try {
            $data = $this->noticeModel->lists($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => $e->getMessage()
            ];
        }

        return array_merge(
            [
                'code' => 0,
                'msg'  => 'success'
            ],
            $data
        );
    }


    public function item($parameter = [])
    {
        if (empty($parameter['id'])) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        try {
            $data = $this->noticeModel->item($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => $e->getMessage()
            ];
        }

        return array_merge(
            [
                'code' => 0,
                'msg'  => 'success'
            ],
            $data
        );
    }


    public function create($parameter = [])
    {
        if (empty($parameter['title']) || empty($parameter['content'])) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        try {
            $this->noticeModel->create($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => $e->getMessage()
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success'
        ];
    }


    public function modify($parameter = [])
    {
        if (empty($parameter['id'])) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        try {
            $this->noticeModel->modify($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => $e->getMessage()
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success'
        ];
    }


    public function remove($parameter = [])
    {
        if (empty($parameter['id'])) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        try {
            $this->noticeModel->remove($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => $e->getMessage()
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success'
        ];
    }

}