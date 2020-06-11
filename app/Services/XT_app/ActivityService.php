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
     * 导入
     * @return int
     */
    public function import($parameter)
    {
        $data['title'] = $parameter['title'];
        $data['content'] = $parameter['content'];
        $data['create_time'] = date('Y-m-d H:i:s', time());

        try {
            $this->activityModel->saveActivity($data);
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
     * 服务端拉取活动
     */
    public function game()
    {
        $result = $this->activityModel->getActivityList();
        if (empty($result)) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        $count = count($result);

        if ($count == 1) {
           return [
               'code' => 0,
               'msg' => 'success',
               'data' => $result[0]
           ];
        }

        return [
            'code' => 0,
            'msg' => 'success',
            'data' => $result
        ];
    }

}