<?php

namespace Xt\Rpc\Controllers;


class DataController extends ControllerBase
{

    /**
     * 获取预付费用户
     * @api {get} /data/get_users_prepay 获取用户预付费get_users_prepay
     * @apiGroup data
     * @apiName get_users_prepay
     *
     * @apiParam {Number} act_id 活动ID
     * @apiParam {Number} [min=0] 最低
     * @apiParam {Number} [max=9999999] 最高
     */
    public function get_users_prepay()
    {
        $parameter['act_id'] = $this->request->query->get('act_id', 0);
        $parameter['zone'] = $this->request->query->get('zone', '');
        $parameter['min'] = $this->request->query->get('min', 0);
        $parameter['max'] = $this->request->query->get('max', 99999999);
        return $this->api('Data', __FUNCTION__, $parameter);
    }


    /**
     * 获取消费用户
     * @api {get} /data/get_users_spend 获取用户消费get_users_spend
     * @apiGroup data
     * @apiName get_users_spend
     *
     * @apiParam {Number} act_id 活动ID
     * @apiParam {Number} [min=0] 最低
     * @apiParam {Number} [max=9999999] 最高
     */
    public function get_users_spend()
    {
        $parameter['act_id'] = $this->request->query->get('act_id', 0);
        $parameter['zone'] = $this->request->query->get('zone', '');
        $parameter['min'] = $this->request->query->get('min', 0);
        $parameter['max'] = $this->request->query->get('max', 99999999);
        return $this->api('Data', __FUNCTION__, $parameter);
    }


    /**
     * 获取登录用户
     * @api {get} /data/get_users_login 获取用户登录次数get_users_login
     * @apiGroup data
     * @apiName get_users_login
     *
     * @apiParam {Number} act_id 活动ID
     * @apiParam {Number} [min=0] 登录次数低限
     * @apiParam {Number} [max=999] 登录次数高限
     */
    public function get_users_login()
    {
        $parameter['act_id'] = $this->request->query->get('act_id', 0);
        $parameter['zone'] = $this->request->query->get('zone', '');
        $parameter['min'] = $this->request->query->get('min', 0);
        $parameter['max'] = $this->request->query->get('max', 999);
        return $this->api('Data', __FUNCTION__, $parameter);
    }


    /**
     * 获取用户等级
     * @api {get} /data/get_users_level 获取用户等级get_users_level
     * @apiGroup data
     * @apiName get_users_level
     *
     * @apiParam {Number} act_id 活动ID
     * @apiParam {Number} [min=0] 最低等级
     * @apiParam {Number} [max=10000] 最高等级
     */
    public function get_users_level()
    {
        $parameter['act_id'] = $this->request->query->get('act_id', 0);
        $parameter['zone'] = $this->request->query->get('zone', '');
        $parameter['min'] = $this->request->query->get('min', 0);
        $parameter['max'] = $this->request->query->get('max', 10000);
        return $this->api('Data', __FUNCTION__, $parameter);
    }

}