<?php

namespace Xt\Rpc\Controllers;


class VoteController extends ControllerBase
{

    /**
     * @api {get} /vote/lists_topic 投票主题列表lists_topic
     * @apiGroup vote
     * @apiName lists_topic
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
     *                "status": "1",
     *                "title": "银河",
     *                "intro": "银河系相关物理知识问答",
     *                "img": "",
     *                "start_time": "2017-04-28 00:00:00",
     *                "end_time": "2017-05-05 00:00:00"
     *            },
     *            {
     *                "id": "102",
     *                "status": "1",
     *                "title": "物种",
     *                "intro": "关于物种起源",
     *                "img": "",
     *                "start_time": "2017-04-28 00:00:00",
     *                "end_time": "2017-05-05 00:00:00"
     *            }
     *        ]
     *    }
     */
    public function lists_topic()
    {
        $parameter['title'] = $this->request->query->get('title');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        $parameter['page'] = $this->request->query->get('page', 1);
        $parameter['size'] = $this->request->query->get('size', 200);
        return $this->api('Vote', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /vote/item_topic 投票主题详情item_topic
     * @apiGroup vote
     * @apiName item_topic
     *
     * @apiParam {Number} id 投票主题ID
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *            "id": "101",
     *            "status": "1",
     *            "title": "银河",
     *            "intro": "银河系相关物理知识问答",
     *            "img": "",
     *            "start_time": "2017-04-28 00:00:00",
     *            "end_time": "2017-05-05 00:00:00"
     *        }
     *    }
     */
    public function item_topic()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Vote', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /vote/create_topic 投票主题创建create_topic
     * @apiGroup vote
     * @apiName create_topic
     *
     * @apiParam {Number} [status=1] 状态
     * @apiParam {Number} title 标题
     * @apiParam {Number} intro 简介
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
    public function create_topic()
    {
        $parameter['status'] = $this->request->query->get('status', 1);
        $parameter['title'] = $this->request->query->get('title');
        $parameter['intro'] = $this->request->query->get('intro');
        $parameter['img'] = $this->request->query->get('img');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        return $this->api('Vote', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /vote/modify_topic 投票主题修改modify_topic
     * @apiGroup vote
     * @apiName modify_topic
     *
     * @apiParam {Number} id 主题ID
     * @apiParam {Number} [status=1] 状态
     * @apiParam {Number} [title] 标题
     * @apiParam {Number} [intro] 简介
     * @apiParam {Number} [img] 图片url
     * @apiParam {Number} [start_time] 开始时间
     * @apiParam {Number} [end_time] 结束时间
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function modify_topic()
    {
        $parameter['id'] = $this->request->query->get('id');
        $parameter['status'] = $this->request->query->get('status', 1);
        $parameter['title'] = $this->request->query->get('title');
        $parameter['intro'] = $this->request->query->get('intro');
        $parameter['img'] = $this->request->query->get('img');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        return $this->api('Vote', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /vote/remove_topic 投票主题删除remove_topic
     * @apiGroup vote
     * @apiName remove_topic
     *
     * @apiParam {Number} id 投票主题ID
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *        "code": 0,
     *        "msg": "success"
     *    }
     */
    public function remove_topic()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Vote', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /vote/lists_option 投票详细lists_option
     * @apiGroup vote
     * @apiName lists_option
     *
     * @apiParam {Number} [group_id] 主题Id
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": [
     *            {
     *                "id": "101",
     *                "subject": "什么是黑洞？",
     *                "answer": "2",
     *                "option_1": "黑球",
     *                "option_2": "宇宙天体",
     *                "option_3": "太阳阴影",
     *                "option_4": "银河系中心"
     *            },
     *            {
     *                "id": "102",
     *                "subject": "弦理论说法正确的是？",
     *                "answer": "3",
     *                "option_1": "小提琴上的弦",
     *                "option_2": "夸克是基本粒子",
     *                "option_3": "能量与物质是可以转化",
     *                "option_4": "粒子"
     *            }
     *        ]
     *    }
     */
    public function lists_option()
    {
        $parameter['group_id'] = $this->request->query->get('group_id');
        return $this->api('Vote', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /vote/item_option 投票选项详情item_option
     * @apiGroup vote
     * @apiName item_option
     *
     * @apiParam {Number} id 投票选项ID
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *            "id": "10101",
     *            "group_id": "101",
     *            "subject": "问题title",
     *            "answer": "3",
     *            "option_1": "选项A",
     *            "option_2": "选项B",
     *            "option_3": "选项C",
     *            "option_4": "选项D",
     *        }
     *    }
     */
    public function item_option()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Vote', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /vote/create_option 投票选项创建create_option
     * @apiGroup vote
     * @apiName create_option
     *
     * @apiParam {Number} group_id 投票项目分组ID
     * @apiParam {String} subject 选项标题
     * @apiParam {Number} [answer] 答案
     * @apiParam {String} option_1 选项
     * @apiParam {String} [option_2] 选项
     * @apiParam {String} [option_3] 选项
     * @apiParam {String} [option_4] 选项
     */
    public function create_option()
    {
        $parameter['group_id'] = $this->request->query->get('group_id');
        $parameter['subject'] = $this->request->query->get('subject');
        $parameter['answer'] = $this->request->query->get('answer');
        $parameter['option_1'] = $this->request->query->get('option_1');
        $parameter['option_2'] = $this->request->query->get('option_2');
        $parameter['option_3'] = $this->request->query->get('option_3');
        $parameter['option_4'] = $this->request->query->get('option_4');
        return $this->api('Vote', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /vote/modify_option 投票选项修改modify_option
     * @apiGroup vote
     * @apiName modify_option
     *
     * @apiParam {Number} id 选项ID
     * @apiParam {Number} group_id 投票项目分组ID
     * @apiParam {String} subject 选项标题
     * @apiParam {Number} [answer] 答案
     * @apiParam {String} option_1 选项
     * @apiParam {String} [option_2] 选项
     * @apiParam {String} [option_3] 选项
     * @apiParam {String} [option_4] 选项
     */
    public function modify_option()
    {
        $parameter['id'] = $this->request->query->get('id');
        $parameter['group_id'] = $this->request->query->get('group_id');
        $parameter['subject'] = $this->request->query->get('subject');
        $parameter['answer'] = $this->request->query->get('answer');
        $parameter['option_1'] = $this->request->query->get('option_1');
        $parameter['option_2'] = $this->request->query->get('option_2');
        $parameter['option_3'] = $this->request->query->get('option_3');
        $parameter['option_4'] = $this->request->query->get('option_4');
        return $this->api('Vote', __FUNCTION__, $parameter);
    }


    /**
     * @api {get} /vote/remove_option 投票选项删除remove_option
     * @apiGroup vote
     * @apiName remove_option
     *
     * @apiParam {Number} id 选项ID
     */
    public function remove_option()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Vote', __FUNCTION__, $parameter);
    }

}