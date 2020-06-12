<?php

namespace Xt\Rpc\Controllers;


class ActivityController extends ControllerBase
{

    /**
     * 列表
     * @api {get} /activity/lists 活动列表lists
     * @apiGroup activity
     * @apiName lists
     *
     * @apiParam {Number} [type] 活动类型
     * @apiParam {String} start_time 开始时间
     * @apiParam {String} end_time 结束时间
     * @apiParam {String} [search] 搜索关键字
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
     *        "count": 20,
     *        "data": [
     *            {
     *                "id": 1,
     *                "zone": "",
     *                "channel": "",
     *                "status": 1,
     *                "type": "prepay",
     *                "visible": 1,
     *                "title": "充值活动",
     *                "content": "活动内容",
     *                "url": "",
     *                "img": "",
     *                "img_small": "",
     *                "custom": "",
     *                "sort": 0,
     *                "start_time": "2017-04-03 01:00:00",
     *                "end_time": "2017-05-03 00:00:00"
     *            }
     *        ]
     *    }
     */
    public function lists()
    {
        $parameter['type'] = $this->request->query->get('type');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        $parameter['search'] = $this->request->query->get('search');
        $parameter['page'] = $this->request->query->get('page', 1);
        $parameter['size'] = $this->request->query->get('size', 200);
        return $this->api('Activity', 'lists', $parameter);
    }


    /**
     * 详情
     * @api {get} /activity/item?id=:id 活动详情item
     * @apiGroup activity
     * @apiName item
     *
     * @apiParam {Number} [id] 活动ID
     *
     * @apiSuccess {Number} code 返回状态
     * @apiSuccess {String} msg 返回消息
     * @apiSuccess {String} data 返回数据
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *            "id": 1,
     *            "zone": "",
     *            "channel": "",
     *            "status": 1,
     *            "sort": 0,
     *            "type": "prepay",
     *            "visible": 1,
     *            "title": "充值活动",
     *            "content": "活动内容",
     *            "url": "",
     *            "img": "",
     *            "img_small": "",
     *            "custom": "",
     *            "start_time": "2017-04-03 01:00:00",
     *            "end_time": "2017-05-03 00:00:00"
     *        }
     *    }
     */
    public function item()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Activity', 'item', $parameter);
    }


    /**
     * 创建
     * @api {get} /activity/create 活动创建create
     * @apiName create
     * @apiGroup activity
     *
     * @apiParam {String} type 活动类型
     * @apiParam {String} zone 区服ID, 默认空代表全服
     * @apiParam {String} channel 渠道, 默认空代表全渠道
     * @apiParam {String} visible 是否可见
     * @apiParam {String} title 标题
     * @apiParam {String} content 内容
     * @apiParam {String} start_time 开始时间
     * @apiParam {String} end_time 结束时间
     * @apiParam {String} [url] 网址
     * @apiParam {String} [img] 图片地址
     * @apiParam {String} [img_small] 小图片
     * @apiParam {String} [custom] 自定义内容
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function create()
    {
        $parameter['status'] = $this->request->query->get('status', 1);
        $parameter['type'] = $this->request->query->get('type');
        $parameter['zone'] = $this->request->query->get('zone');
        $parameter['channel'] = $this->request->query->get('channel');
        $parameter['visible'] = $this->request->query->get('visible', 1);
        $parameter['title'] = $this->request->query->get('title');
        $parameter['content'] = $this->request->query->get('content');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        $parameter['url'] = $this->request->query->get('url');
        $parameter['img'] = $this->request->query->get('img');
        $parameter['img_small'] = $this->request->query->get('img_small');
        $parameter['custom'] = $this->request->query->get('custom');
        return $this->api('Activity', 'create', $parameter);
    }


    /**
     * 修改
     * @api {get} /activity/modify 活动修改modify
     * @apiName modify
     * @apiGroup activity
     *
     * @apiParam {Number} id 活动ID
     * @apiParam {String} [zone] 区服ID, 默认空代表全服
     * @apiParam {String} [channel] 渠道, 默认空代表全渠道
     * @apiParam {Number} [status] 启用状态 1|0
     * @apiParam {Number} [sort] 排序默认0
     * @apiParam {String} [type] 活动类型
     * @apiParam {String} [visible] 是否可见
     * @apiParam {String} [title] 标题
     * @apiParam {String} [content] 内容
     * @apiParam {String} [start_time] 开始时间
     * @apiParam {String} [end_time] 结束时间
     * @apiParam {String} [url] 网址
     * @apiParam {String} [img] 图片地址
     * @apiParam {String} [img_small] 小图片
     * @apiParam {String} [custom] 自定义内容
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function modify()
    {
        // 整形数据必须有默认值
        $parameter['id'] = $this->request->query->get('id');
        $parameter['zone'] = $this->request->query->get('zone');
        $parameter['channel'] = $this->request->query->get('channel');
        $parameter['type'] = $this->request->query->get('type');
        $parameter['status'] = $this->request->query->get('status', 1);
        $parameter['sort'] = $this->request->query->get('sort', 0);
        $parameter['visible'] = $this->request->query->get('visible', 1);
        $parameter['title'] = $this->request->query->get('title');
        $parameter['content'] = $this->request->query->get('content');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        $parameter['url'] = $this->request->query->get('url');
        $parameter['img'] = $this->request->query->get('img');
        $parameter['img_small'] = $this->request->query->get('img_small');
        $parameter['custom'] = $this->request->query->get('custom');
        return $this->api('Activity', 'modify', $parameter);
    }


    /**
     * 删除
     * @api {get} /activity/remove?id=:id 活动删除remove
     * @apiName remove
     * @apiGroup activity
     *
     * @apiParam {Number} [id] 活动ID
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function remove()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Activity', 'remove', $parameter);
    }


    /**
     * 活动配置列表
     * @api {get} /activity/lists_cfg?item_id=:item_id 活动配置列表lists_cfg
     * @apiGroup activity
     * @apiName lists_cfg
     *
     * @apiParam {Number} [item_id] 活动ID
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": [
     *            {
     *                "id": "5",
     *                "item_id": "100",
     *                "step": "100",
     *                "prop": "11111",
     *                "sort": "0",
     *                "title": "充值100奖励",
     *                "content": null
     *            },
     *            {
     *                "id": "8",
     *                "item_id": "100",
     *                "step": "200",
     *                "prop": "22222",
     *                "sort": "0",
     *                "title": "充值200奖励",
     *                "content": null
     *            }
     *        ]
     *    }
     */
    public function lists_cfg()
    {
        $parameter['item_id'] = $this->request->query->get('item_id');
        return $this->api('Activity', 'lists_cfg', $parameter);
    }


    /**
     * 活动配置详情
     * @api {get} /activity/item_cfg?id=:id 活动配置详情item_cfg
     * @apiGroup activity
     * @apiName item_cfg
     *
     * @apiParam {Number} [id] 活动配置ID
     *
     * @apiSuccess {Number} code 返回状态
     * @apiSuccess {String} msg 返回消息
     * @apiSuccess {Number} item_id 活动ID
     * @apiSuccess {Number} step 步长
     * @apiSuccess {String} prop 礼物
     * @apiSuccess {Number} sort 排序
     * @apiSuccess {String} title 标题
     * @apiSuccess {String} content 内容
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *            "id": "8",
     *            "item_id": "10",
     *            "step": "100",
     *            "prop": "123456",
     *            "sort": "0",
     *            "title": "消费100奖励",
     *            "content": null
     *        }
     *    }
     */
    public function item_cfg()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Activity', 'item_cfg', $parameter);
    }


    /**
     * 活动配置创建
     * @api {get} /activity/create_cfg 活动配置创建create_cfg
     * @apiGroup activity
     * @apiName create_cfg
     *
     * @apiParam {Number} item_id 活动ID
     * @apiParam {Number} step 步长(档位)
     * @apiParam {String} prop 礼物
     * @apiParam {Number} [sort=0] 排序
     * @apiParam {String} [title] 标题
     * @apiParam {String} [content] 内容
     *
     * @apiSuccessExample 成功响应:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function create_cfg()
    {
        $parameter['item_id'] = $this->request->query->get('item_id');
        $parameter['step'] = $this->request->query->get('step');
        $parameter['prop'] = $this->request->query->get('prop');
        $parameter['sort'] = $this->request->query->get('sort');
        $parameter['title'] = $this->request->query->get('title');
        $parameter['content'] = $this->request->query->get('content');
        //$parameter['remark'] = $this->request->query->get('remark');
        return $this->api('Activity', 'create_cfg', $parameter);
    }


    /**
     * 活动配置修改
     * @api {get} /activity/modify_cfg 活动配置修改modify_cfg
     * @apiGroup activity
     * @apiName modify_cfg
     *
     * @apiParam {Number} id 配置ID
     * @apiParam {Number} [item_id] 活动ID
     * @apiParam {Number} [step] 步长(档位)
     * @apiParam {String} [prop] 礼物
     * @apiParam {Number} [sort=0] 排序
     * @apiParam {String} [title] 标题
     * @apiParam {String} [content] 内容
     *
     * @apiSuccessExample 成功响应:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function modify_cfg()
    {
        $parameter['id'] = $this->request->query->get('id');
        $parameter['item_id'] = $this->request->query->get('item_id');
        $parameter['step'] = $this->request->query->get('step');
        $parameter['prop'] = $this->request->query->get('prop');
        $parameter['sort'] = $this->request->query->get('sort');
        $parameter['title'] = $this->request->query->get('title');
        $parameter['content'] = $this->request->query->get('content');
        //$parameter['remark'] = $this->request->query->get('remark');
        return $this->api('Activity', 'modify_cfg', $parameter);
    }


    /**
     * 活动配置删除
     * @api {get} /activity/remove_cfg 活动配置删除remove_cfg
     * @apiGroup activity
     * @apiName remove_cfg
     *
     * @apiParam {Number} id 配置ID
     *
     * @apiSuccessExample 成功响应:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function remove_cfg()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Activity', 'remove_cfg', $parameter);
    }


    /**
     * 活动日志
     * @api {get} /activity/logs 活动日志logs
     * @apiGroup activity
     * @apiName logs
     *
     * @apiParam {Number} [item_id] 活动ID
     * @apiParam {Number} [cfg_id] 活动配置ID
     * @apiParam {String} [zone] 服务器ID
     * @apiParam {String} [user_id] 玩家ID
     * @apiParam {String} [prop] 礼物关键字(模糊搜索)
     * @apiParam {String} [start_time] 开始时间UTC 格式Y-m-d H:i:s
     * @apiParam {String} [end_time] 结束时间UTC 格式Y-m-d H:i:s
     * @apiParam {Number} [page=1] 页码
     * @apiParam {Number} [size=200] 单页大小
     *
     * @apiSuccess {Number} code 返回状态
     * @apiSuccess {String} msg 返回消息
     * @apiSuccess {Number} item_id 活动ID
     * @apiSuccess {String} cfg_id 活动配置ID
     * @apiSuccess {String} user_id 用户ID
     * @apiSuccess {String} prop 礼包
     * @apiSuccess {String} create_time 日志时间
     */
    public function logs()
    {
        $parameter['item_id'] = $this->request->query->get('item_id', 0);
        $parameter['cfg_id'] = $this->request->query->get('cfg_id', 0);
        $parameter['zone'] = $this->request->query->get('zone');
        $parameter['user_id'] = $this->request->query->get('user_id');
        $parameter['prop'] = $this->request->query->get('prop');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        $parameter['page'] = $this->request->query->get('page', 1);
        $parameter['size'] = $this->request->query->get('size', 200);
        return $this->api('Activity', 'logs', $parameter);
    }

    /**
     * 活动保存
     * @api {get} /activity/import 活动保存import
     * @apiGroup activity
     * @apiName import
     *
     * @apiParam {string} [title] 活动ID
     * @apiParam {string} [content] 活动配置ID
     */
    public function import()
    {
        $parameter['title'] = $this->request->query->get('title');
        $parameter['content'] = $this->request->query->get('content');
        $parameter['zone'] = $this->request->query->get('zone');
        return $this->api('Activity', 'import', $parameter);
    }

    /**
     * 服务端拉取活动
     * @api {get} /activity/game 服务端拉取活动game
     *
     */
    public function game()
    {
        $parameter['zone'] = $this->request->query->get('zone');
        return $this->api('Activity', 'game', $parameter);
    }

    /**
     * 测试api
     * msg : 信息
     */
    public function test()
    {
        $parameter['msg'] = $this->request->query->get('msg');
        return $this->api('Activity', 'test', $parameter);
    }

}