<?php

namespace Xt\Rpc\Controllers;


class PropController extends ControllerBase
{

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        return $this->api('Prop', $name, $this->request->query->all());
    }


    /**
     * 可操作属性列表
     * @api {get} /prop/attribute 道具可操作属性列表attribute
     * @apiGroup prop
     * @apiName attribute
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *            "coin": "货币",
     *            "exp": "经验",
     *            "mail": "邮件"
     *        }
     *    }
     */
    public function attribute()
    {
        return $this->api('Prop', __FUNCTION__, []);
    }


    /**
     * 道具货币操作
     * @api {get} /prop/coin 道具货币操作coin
     * @apiGroup prop
     * @apiName coin
     *
     * @apiParam {String} zone 区服ID
     * @apiParam {String} user_id 玩家ID
     * @apiParam {Number} amount 增加数量(负数则减少)
     * @apiParam {String} [msg] 消息
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function coin()
    {
        $parameter['zone'] = $this->request->query->get('zone');
        $parameter['user_id'] = $this->request->query->get('user_id');
        $parameter['amount'] = $this->request->query->get('amount');
        $parameter['msg'] = $this->request->query->get('msg');
        return $this->api('Prop', __FUNCTION__, $parameter);
    }


    /**
     * 道具经验操作
     * @api {get} /prop/exp 道具经验操作exp
     * @apiGroup prop
     * @apiName exp
     *
     * @apiParam {String} zone 区服ID
     * @apiParam {String} user_id 玩家ID
     * @apiParam {Number} amount 增加数量(负数则减少)
     * @apiParam {String} [msg] 消息
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function exp()
    {
        $parameter['zone'] = $this->request->query->get('zone');
        $parameter['user_id'] = $this->request->query->get('user_id');
        $parameter['amount'] = $this->request->query->get('amount');
        $parameter['msg'] = $this->request->query->get('msg');
        return $this->api('Prop', __FUNCTION__, $parameter);
    }


    /**
     * 道具操作
     * @api {get} /prop/attach 道具操作attach
     * @apiGroup prop
     * @apiName attach
     *
     * @apiParam {String} zone 区服ID
     * @apiParam {String} user_id 玩家ID
     * @apiParam {String} attach 道具内容(道具1*数量,道具2*数量) 例: 123456*10,654321*5
     * @apiParam {String} [msg] 消息
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function attach()
    {
        $parameter['zone'] = $this->request->query->get('zone');
        $parameter['user_id'] = $this->request->query->get('user_id');
        $parameter['attach'] = $this->request->query->get('attach');
        $parameter['msg'] = $this->request->query->get('msg');
        return $this->api('Prop', __FUNCTION__, $parameter);
    }


    /**
     * 道具邮件(消息)操作
     * @api {get} /prop/mail 道具邮件操作mail
     * @apiGroup prop
     * @apiName mail
     *
     * @apiParam {String} zone 区服ID
     * @apiParam {String} user_id 玩家ID
     * @apiParam {String} amount 发奖内容
     * @apiParam {String} title 标题
     * @apiParam {String} msg 邮件内容
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function mail()
    {
        $parameter['zone'] = $this->request->query->get('zone');
        $parameter['user_id'] = $this->request->query->get('user_id');
        $parameter['title'] = $this->request->query->get('title');
        $parameter['msg'] = $this->request->query->get('msg');
        $parameter['amount'] = $this->request->query->get('amount');
        return $this->api('Prop', __FUNCTION__, $parameter);
    }

    /**
     * 全服邮件
     * @api {get} /prop/attachServer 全服道具邮件操作mail
     * @apiGroup prop
     * @apiName mail
     *
     * @apiParam {String} zone 区服ID
     * @apiParam {String} amount 发奖内容
     * @apiParam {String} title 标题
     * @apiParam {String} msg 邮件内容
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function attachServer()
    {
        $parameter['zone']      = $this->request->query->get('zone');
        $parameter['title']     = $this->request->query->get('title');
        $parameter['msg']       = $this->request->query->get('msg');
        $parameter['attach']    = $this->request->query->get('attach');
        $parameter['award_url'] = $this->di['db_cfg']['game_url']['award_url'];
        return $this->api('Prop', __FUNCTION__, $parameter);
    }
}