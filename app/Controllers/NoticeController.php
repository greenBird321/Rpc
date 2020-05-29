<?php

namespace Xt\Rpc\Controllers;


class NoticeController extends ControllerBase
{

    /**
     * @api {get} /notice/lists 公告列表lists
     * @apiGroup notice
     * @apiName lists
     *
     * @apiParam {Number} [title] 活动类型
     * @apiParam {String} [start_time] 开始时间
     * @apiParam {String} [end_time] 结束时间
     * @apiParam {Number} [page=1] 页码
     * @apiParam {Number} [size=200] 单页条数
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "count": "2",
     *        "data": [
     *            {
     *                "id": "101",
     *                "zone": "",
     *                "channel": "",
     *                "status": "1",
     *                "sort": "0",
     *                "title": "五一假期公告",
     *                "content": "放假了",
     *                "img": "",
     *                "start_time": "2017-04-28 00:00:00",
     *                "end_time": "2017-05-05 00:00:00"
     *            },
     *            {
     *                "id": "102",
     *                "zone": "",
     *                "channel": "",
     *                "status": "1",
     *                "sort": "0",
     *                "title": "工作时间",
     *                "content": "放完假就上班",
     *                "img": "",
     *                "start_time": "2017-04-28 00:00:00",
     *                "end_time": "2017-05-05 00:00:00"
     *            }
     *        ]
     *    }
     */
    public function lists()
    {
        $parameter['title'] = $this->request->query->get('title');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        $parameter['page'] = $this->request->query->get('page', 1);
        $parameter['size'] = $this->request->query->get('size', 200);
        return $this->api('Notice', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /notice/item 公告详细item
     * @apiGroup notice
     * @apiName item
     *
     * @apiParam {Number} [id] 公告ID
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *            "id": "2",
     *            "zone": "",
     *            "channel": "",
     *            "status": "1",
     *            "sort": "0",
     *            "title": "工作时间",
     *            "content": "放完假就上班",
     *            "img": "",
     *            "start_time": "2017-04-28 00:00:00",
     *            "end_time": "2017-05-05 00:00:00"
     *        }
     *    }
     */
    public function item()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Notice', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /notice/create 公告创建create
     * @apiGroup notice
     * @apiName create
     *
     * @apiParam {String} [zone] 区服
     * @apiParam {String} [channel] 渠道
     * @apiParam {Number} [status=1] 状态
     * @apiParam {Number} [sort] 排序
     * @apiParam {Number} title 标题
     * @apiParam {Number} content 内容
     * @apiParam {Number} [img] 图片url
     * @apiParam {Number} start_time 开始时间
     * @apiParam {Number} end_time 结束时间
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
        $parameter['zone'] = $this->request->query->get('zone');
        $parameter['channel'] = $this->request->query->get('channel');
        $parameter['status'] = $this->request->query->get('status', 1);
        $parameter['sort'] = $this->request->query->get('sort', 0);
        $parameter['title'] = $this->request->query->get('title');
        $parameter['content'] = $this->request->query->get('content');
        $parameter['img'] = $this->request->query->get('img');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        return $this->api('Notice', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /notice/modify 公告修改modify
     * @apiGroup notice
     * @apiName modify
     *
     * @apiParam {Number} id 公告ID
     * @apiParam {String} [zone] 区服
     * @apiParam {String} [channel] 渠道
     * @apiParam {Number} [status=1] 状态
     * @apiParam {Number} [sort] 排序
     * @apiParam {Number} title 标题
     * @apiParam {Number} content 内容
     * @apiParam {Number} [img] 图片url
     * @apiParam {Number} start_time 开始时间
     * @apiParam {Number} end_time 结束时间
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
        $parameter['id'] = $this->request->query->get('id');
        $parameter['zone'] = $this->request->query->get('zone');
        $parameter['channel'] = $this->request->query->get('channel');
        $parameter['status'] = $this->request->query->get('status', 1);
        $parameter['sort'] = $this->request->query->get('sort', 0);
        $parameter['title'] = $this->request->query->get('title');
        $parameter['content'] = $this->request->query->get('content');
        $parameter['img'] = $this->request->query->get('img');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        return $this->api('Notice', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /notice/remove 公告删除remove
     * @apiGroup notice
     * @apiName remove
     *
     * @apiParam {Number} id 公告ID
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function remove()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Notice', __FUNCTION__, $parameter);
    }

}