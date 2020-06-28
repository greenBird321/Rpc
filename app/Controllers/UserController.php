<?php

namespace Xt\Rpc\Controllers;


class UserController extends ControllerBase
{

    /**
     * 用户信息
     * @api {get} /user/profile?zone=100&user_id=:user_id 用户信息profile
     * @apiGroup user
     * @apiName profile
     *
     * @apiParam {String} zone 服务器ID
     * @apiParam {String} [user_id] 玩家ID
     * @apiParam {String} [name] 用户昵称
     * @apiParam {String} [account_id] 平台账号ID
     *
     * @apiSuccess {Number} code 返回状态
     * @apiSuccess {String} msg 返回消息
     * @apiSuccess {Number} count 返回结果数量
     * @apiSuccess {String} data 返回数据
     * @apiSuccess {String} account_id 平台账号ID
     * @apiSuccess {String} user_id 用户ID
     * @apiSuccess {String} name 用户昵称
     * @apiSuccess {String} level 等级
     * @apiSuccess {String} exp 经验
     * @apiSuccess {String} coin 货币
     * @apiSuccess {String} vip VIP积分
     * @apiSuccess {String} create_time 创建时间
     * @apiSuccess {String} attribute 其他属性,需遍历展示
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *            "account_id": "1001234",
     *            "user_id": "123456",
     *            "name": "丹妮",
     *            "level": "32",
     *            "exp": "0",
     *            "coin": "232",
     *            "vip": "120",
     *            "create_time": "2016-08-30 23:13:25 +0000",
     *            "attribute": {
     *            }
     *        }
     *    }
     *
     */
    public function profile()
    {
        $parameter['zone']       = $this->request->query->get('zone');
        $parameter['user_id']    = $this->request->query->get('user_id');
        $parameter['name']       = $this->request->query->get('name');
        $parameter['account_id'] = $this->request->query->get('account_id');
        return $this->api('User', __FUNCTION__, $parameter);
    }

    /**
     * 用户消耗查询
     * @api {get} /user/propinfo?zone=100&user_id=:user_id 用户信息
     * @apiGroup user
     * @apiName propinfo
     *
     * @apiParam {String} zone 服务器ID
     * @apiParam {String} [user_id] 玩家ID
     * @apiParam {String} [action_id] 行为id
     * @apiParam {String} [status] 0: 失去道具 1: 获得道具 2: 全部道具
     *
     * @apiSuccess {Number} code 返回状态
     * @apiSuccess {String} msg 返回消息
     * @apiSuccess {String} data 返回数据
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *
     *        }
     *    }
     *
     */
    public function propinfo()
    {
        $parameter['zone']      = $this->request->query->get('zone');
        $parameter['user_id']   = $this->request->query->get('user_id');
        $parameter['action_id'] = $this->request->query->get('action_id');
        $parameter['status']    = $this->request->query->get('status');

        return $this->api('User', __FUNCTION__, $parameter);
    }

    /**
     * 查询玩家的账号id
     */
    public function userinfo()
    {
        $parameter['zone']    = $this->request->query->get('zone');
        $parameter['name']    = $this->request->query->get('name');
        $parameter['role_id'] = $this->request->query->get('role_id');

        return $this->api('User', __FUNCTION__, $parameter);
    }
}