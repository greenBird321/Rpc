<?php

namespace Xt\Rpc\Controllers;


class ZoneController extends ControllerBase
{


    /**
     * 区服列表
     * @api {get} /zone/lists 区服列表lists
     * @apiGroup zone
     * @apiName lists
     *
     * @apiSuccess {Number} code 返回状态
     * @apiSuccess {String} msg 返回消息
     * @apiSuccess {String} id 区服标识ID
     * @apiSuccess {String} name 名称
     * @apiSuccess {String} host 服务器IP
     * @apiSuccess {String} port 端口
     * @apiSuccess {String} status 状态 on,off,sandbox
     * @apiSuccess {String} tag 标签 usual,recommend,new,hot
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": [
     *            {
     *                "id": "2001",
     *                "name": "s2001服",
     *                "host": "192.168.2.1",
     *                "port": "95277",
     *                "status": "on",
     *                "tag": "new"
     *            },
     *            {
     *                "id": "2002",
     *                "name": "s2002服",
     *                "host": "192.2.2.2",
     *                "port": "95277",
     *                "status": "off",
     *                "tag": "usual"
     *            }
     *        ]
     *    }
     */
    public function lists()
    {
        return $this->api('Zone', __FUNCTION__, []);
    }


    /**
     * 区服详细
     * @api {get} /zone/item 区服详细item
     * @apiGroup zone
     * @apiName item
     *
     * @apiParam {String} id 区服标识ID
     *
     * @apiSuccess {Number} code 返回状态
     * @apiSuccess {String} msg 返回消息
     * @apiSuccess {String} id 区服标识ID
     * @apiSuccess {String} name 名称
     * @apiSuccess {String} host 服务器IP
     * @apiSuccess {String} port 端口
     * @apiSuccess {String} status 状态 on,off,sandbox
     * @apiSuccess {String} tag 标签 usual,recommend,new,hot
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *            "id": "2017",
     *            "name": "S2017",
     *            "host": "192.168.1.99",
     *            "port": "0",
     *            "status": "on",
     *            "tag": "new"
     *        }
     *    }
     */
    public function item()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Zone', __FUNCTION__, $parameter);
    }


    /**
     * 区服创建
     * @api {get} /zone/create 区服创建create
     * @apiGroup zone
     * @apiName create
     *
     * @apiParam {String} id 区服标识ID
     * @apiParam {String} name 区服名称
     * @apiParam {String} host IP地址
     * @apiParam {String} [port] 端口
     * @apiParam {String} status=on 状态 on,off,sandbox
     * @apiParam {String} tag=usual 标签usual,recommend,new,hot
     * @apiParam {String} custom 拓展字段
     *
     * @apiSuccessExample Success-Response:
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     *
     */
    public function create()
    {
        $parameter['id'] = $this->request->query->get('id');
        $parameter['name'] = $this->request->query->get('name');
        $parameter['host'] = $this->request->query->get('host');
        $parameter['port'] = $this->request->query->get('port');
        $parameter['status'] = $this->request->query->get('status', 'on');
        $parameter['tag'] = $this->request->query->get('tag', 'usual');
        $parameter['key'] = $this->request->query->get('key');
        $parameter['custom'] = $this->request->query->get('custom');
        $parameter['open_mode'] = $this->request->query->get('open_mode');
        $parameter['color_mode'] = $this->request->query->get('color_mode');
        $parameter['full_mode'] = $this->request->query->get('full_mode');
        $parameter['visible_mode'] = $this->request->query->get('visible_mode');
        return $this->api('Zone', __FUNCTION__, $parameter);
    }


    /**
     * 区服编辑
     * @api {get} /zone/modify 区服创建modify
     * @apiGroup zone
     * @apiName modify
     *
     * @apiParam {String} id 区服标识ID
     * @apiParam {String} name 区服名称
     * @apiParam {String} host IP地址
     * @apiParam {String} [port] 端口
     * @apiParam {String} status=on 状态 on,off,sandbox
     * @apiParam {String} tag=usual 标签usual,recommend,new,hot
     * @apiParam {String} custom 拓展字段
     *
     * @apiSuccessExample Success-Response:
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     *
     */
    public function modify()
    {
        $parameter['id'] = $this->request->query->get('id');
        $parameter['name'] = $this->request->query->get('name');
        $parameter['host'] = $this->request->query->get('host');
        $parameter['port'] = $this->request->query->get('port');
        $parameter['status'] = $this->request->query->get('status');
        $parameter['tag'] = $this->request->query->get('tag');
        $parameter['custom'] = $this->request->query->get('custom');
        $parameter['open_mode'] = $this->request->query->get('open_mode');
        $parameter['color_mode'] = $this->request->query->get('color_mode');
        $parameter['full_mode'] = $this->request->query->get('full_mode');
        $parameter['visible_mode'] = $this->request->query->get('visible_mode');
        return $this->api('Zone', __FUNCTION__, $parameter);
    }


    /**
     * 区服编辑
     * @api {get} /zone/remove 区服删除remove
     * @apiGroup zone
     * @apiName remove
     *
     * @apiParam {String} id 区服标识ID
     *
     * @apiSuccessExample Success-Response:
     *    {
     *        "code": 0,
     *        "msg": "success"
     *    }
     *
     */
    public function remove()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Zone', __FUNCTION__, $parameter);
    }

}