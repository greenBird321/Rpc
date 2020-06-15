<?php

/**
 * 活动相关
 * Class ActivityService
 */
namespace Xt\Rpc\Services\XT_app;


use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Activity;
use Exception;

class ActivityService extends Service
{

    private $activityModel;


    public function __construct($di)
    {
        parent::__construct($di);
        $this->activityModel = new Activity();
    }


    /**
     * 列表
     * @param $parameter
     * @return array
     */
    public function lists($parameter)
    {
        try {
            $data = $this->activityModel->lists($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
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


    /**
     * 详情
     * @param $parameter
     * @return array
     */
    public function item($parameter)
    {
        try {
            $data = $this->activityModel->item($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        if (!$data) {
            return ['code' => 1, 'msg' => 'no data'];
        }

        return [
            'code' => 0,
            'msg'  => 'success',
            'data' => $data
        ];
    }


    /**
     * 创建
     * @param $parameter
     * @return array
     */
    public function create($parameter)
    {
        try {
            $this->activityModel->create($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success'
        ];
    }


    /**
     * 修改
     * @param $parameter
     * @return array
     */
    public function modify($parameter)
    {
        try {
            $this->activityModel->modify($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success'
        ];
    }


    /**
     * 删除
     * @param $parameter
     * @return array
     */
    public function remove($parameter)
    {
        try {
            $this->activityModel->remove($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success'
        ];
    }


    /**
     * 活动配置列表
     * @param $parameter
     * @return array
     */
    public function lists_cfg($parameter)
    {
        if (!$parameter['item_id']) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        try {
            $data = $this->activityModel->lists_cfg($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
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


    /**
     * 活动配置详细
     * @param $parameter
     * @return array
     */
    public function item_cfg($parameter)
    {
        if (!$parameter['id']) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        try {
            $data = $this->activityModel->item_cfg($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return array_merge([
            'code' => 0,
            'msg'  => 'success'
        ], $data);
    }


    /**
     * 活动配置创建
     * @param $parameter
     * @return array
     */
    public function create_cfg($parameter)
    {
        if (!$parameter['item_id'] || !$parameter['step'] || !$parameter['prop']) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        try {
            $this->activityModel->create_cfg($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success'
        ];
    }


    /**
     * 活动配置修改
     * @param $parameter
     * @return array
     */
    public function modify_cfg($parameter)
    {
        if (!$parameter['id']) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        try {
            $this->activityModel->modify_cfg($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success'
        ];
    }


    /**
     * 活动配置删除
     * @param $parameter
     * @return array
     */
    public function remove_cfg($parameter)
    {
        if (!$parameter['id']) {
            return [
                'code' => 1,
                'msg'  => 'missing parameter'
            ];
        }

        try {
            $this->activityModel->remove_cfg($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return [
            'code' => 0,
            'msg'  => 'success'
        ];
    }


    /**
     * 活动日志
     * @param $parameter
     * @return array
     */
    public function logs($parameter)
    {
        try {
            $data = $this->activityModel->logs($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
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


    /**
     * 活动保存并上报服务端
     * @return int
     */
    public function import($parameter)
    {
        $data['title'] = $parameter['title'];
        $data['content'] = $parameter['content'];
        $data['zone'] = $parameter['zone'];
        $data['create_time'] = date('Y-m-d H:i:s', time());

        try {
            $this->activityModel->saveActivity($data);
            // 发送给服务端将活动

        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg'  => 'failed'
            ];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    /**
     * 导出
     */
    public function export()
    {
    }

    /**
     * 服务端主动拉取活动
     */
    public function game($parameter)
    {
        $result = $this->activityModel->getActivityList($parameter['zone']);
        if (empty($result)) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        $data = [];
        foreach ($result as $key => $value) {
            $data[] = [
                'title' => $value['title'],
                'content' => base64_encode($value['content'])
            ];
        }
        return [
            'code' => 0,
            'msg' => 'success',
            'zone' => $parameter['zone'],
            'data' => $data
        ];
    }
}