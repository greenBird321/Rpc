<?php

/**
 * 投票相关
 * Class VoteService
 */
namespace Xt\Rpc\Services\XT_app;


use Exception;
use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Vote;


class VoteService extends Service
{

    private $voteModel;


    public function __construct($di)
    {
        parent::__construct($di);
        $this->voteModel = new Vote();
    }


    /**
     * 投票主题列表
     * @param $parameter
     * @return array
     */
    public function lists_topic($parameter)
    {
        try {
            $data = $this->voteModel->lists_topic($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }


    /**
     * 投票主题详细
     * @param $parameter
     * @return array
     */
    public function item_topic($parameter)
    {
        if (!$parameter['id']) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        try {
            $data = $this->voteModel->item_topic($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }


    /**
     * 投票主题创建
     * @param $parameter
     * @return array
     */
    public function create_topic($parameter)
    {
        if (!$parameter['title']) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->voteModel->create_topic($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }


    /**
     * 投票主题修改
     * @param $parameter
     * @return array
     */
    public function modify_topic($parameter)
    {
        if (!$parameter['id']) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        try {
            $this->voteModel->modify_topic($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }


    /**
     * 投票主题删除
     * @param $parameter
     * @return array
     */
    public function remove_topic($parameter)
    {
        if (!$parameter['id']) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        try {
            $this->voteModel->remove_topic($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }


    /**
     * 投票选项列表
     * @param $parameter
     * @return array
     */
    public function lists_option($parameter)
    {
        if (!$parameter['group_id']) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        try {
            $data = $this->voteModel->lists_option($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }


    /**
     * 投票选项详情
     * @param $parameter
     * @return array
     */
    public function item_option($parameter)
    {
        if (!$parameter['id']) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        try {
            $data = $this->voteModel->item_option($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }


    /**
     * 投票选项创建
     * @param $parameter
     * @return array
     */
    public function create_option($parameter)
    {
        if (!$parameter['group_id'] || !$parameter['subject']) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        try {
            $this->voteModel->create_option($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }


    /**
     * 投票选项修改
     * @param $parameter
     * @return array
     */
    public function modify_option($parameter)
    {
        if (!$parameter['id']) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        try {
            $this->voteModel->modify_option($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }


    /**
     * 投票选项删除
     * @param $parameter
     * @return array
     */
    public function remove_option($parameter)
    {
        if (!$parameter['id']) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        try {
            $this->voteModel->remove_option($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => 'failed'];
        }
        return ['code' => 0, 'msg' => 'success'];
    }
}