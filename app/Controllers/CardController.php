<?php

namespace Xt\Rpc\Controllers;


class CardController extends ControllerBase
{

    /**
     * 列表
     * @api {get} /card/lists 礼品卡列表lists
     * @apiGroup card
     * @apiName lists
     *
     * @apiParam {String} [type] 活动类型 cash现金卡, discount折扣卡, prop道具卡
     * @apiParam {String} [title] 标题搜索关键字
     * @apiParam {Number} [page=1] 页码
     * @apiParam {Number} [size=200] 单页条数
     *
     * @apiSuccess {Number} code 返回状态
     * @apiSuccess {String} msg 返回消息
     * @apiSuccess {Number} count 总记录数
     * @apiSuccess {String} data 返回数据
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "code": 0,
     *        "msg": "success",
     *        "count": "2",
     *        "data": [
     *            {
     *                "id": "14",
     *                "type": "cash",
     *                "data": "30", // 数值或道具
     *                "title": "现金卡",
     *                "limit_times": "2",
     *                "expired_in": "2017-07-01 00:00:00"
     *            },
     *            {
     *                "id": "11",
     *                "type": "discount",
     *                "data": "30", // 数值或道具
     *                "title": "折扣卡",
     *                "limit_times": "0",
     *                "expired_in": "2017-07-01 00:00:00"
     *            },
     * {
     *                "id": "1",
     *                "type": "register",
     *                "data": "0", // 数值或道具
     *                "title": "taptap注册码",
     *                "limit_times": "0",
     *                "expired_in": "2017-07-01 00:00:00"
     *            },
     *        ]
     *    }
     */
    public function lists()
    {
        $parameter['type'] = $this->request->query->get('type');
        $parameter['title'] = $this->request->query->get('title');
        return $this->api('Card', __FUNCTION__, $parameter);
    }


    /**
     * 详细
     * @api {get} /card/item 礼品卡详细item
     * @apiGroup card
     * @apiName item
     *
     * @apiParam {Number} id 礼品卡主题ID
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *            "id": "14",
     *            "type": "discount",
     *            "data": "30", // 数值或道具
     *            "title": "五一折扣卡",
     *            "intro": "折扣卡活动详细介绍",
     *            "limit_times": "0",
     *            "expired_in": "2017-07-01 00:00:00"
     *        }
     *    }
     */
    public function item()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Card', __FUNCTION__, $parameter);
    }


    /**
     * 创建
     * @api {get} /card/create 礼品卡创建create
     * @apiGroup card
     * @apiName create
     *
     * @apiParam {String} title 标题
     * @apiParam {String} type 活动类型 cash现金卡, discount折扣卡, prop道具卡
     * @apiParam {String} data 数值或道具
     * @apiParam {String} [intro] 介绍
     * @apiParam {String} limit_times 每个人参与次数限制
     * @apiParam {String} expired_in 过期时间 例: 2017-07-01 00:00:00
     * @apiParam {String} count 礼品卡数量
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function create()
    {
        $parameter['type'] = $this->request->query->get('type');
        $parameter['data'] = $this->request->query->get('data');
        $parameter['title'] = $this->request->query->get('title');
        $parameter['intro'] = $this->request->query->get('intro');
        $parameter['limit_times'] = $this->request->query->get('limit_times');
        $parameter['code_limit_times'] = $this->request->query->get('code_limit_times');
        $parameter['expired_in'] = $this->request->query->get('expired_in');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['count'] = $this->request->query->get('count');
        return $this->api('Card', __FUNCTION__, $parameter);
    }


    /**
     * 修改
     * @api {get} /card/modify 礼品卡修改modify
     * @apiGroup card
     * @apiName modify
     *
     * @apiParam {String} id 主题ID
     * @apiParam {String} [title] 标题
     * @apiParam {String} [data] 数值或道具
     * @apiParam {String} [intro] 介绍
     * @apiParam {String} limit_times 每个人参与次数限制
     * @apiParam {String} [expired_in] 过期时间 例: 2017-07-01 00:00:00
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function modify()
    {
        $parameter['id'] = $this->request->query->get('id');
        $parameter['data'] = $this->request->query->get('data');
        $parameter['title'] = $this->request->query->get('title');
        $parameter['intro'] = $this->request->query->get('intro');
        $parameter['limit_times'] = $this->request->query->get('limit_times');
        $parameter['expired_in'] = $this->request->query->get('expired_in');
        $parameter['start_time'] = $this->request->query->get('start_time');
        return $this->api('Card', __FUNCTION__, $parameter);
    }


    /**
     * 删除
     * @api {get} /card/remove 礼品卡删除remove
     * @apiGroup card
     * @apiName remove
     *
     * @apiParam {String} id 主题ID
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function remove()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Card', __FUNCTION__, $parameter);
    }

}