<?php

namespace Xt\Rpc\Controllers;


class StatsController extends ControllerBase
{


    /**
     * 统计实时数据
     * @api {get} /stats/realTime 统计实时数据
     * @apiGroup stats
     * @apiName realTime
     *
     * @apiParam {String} date 日期
     * @apiParam {String} [channel] 固定true
     *
     */
    public function realTime()
    {
        $parameter['date'] = $this->request->query->get('date');
        $parameter['channel'] = $this->request->query->get('channel');
        return $this->api('Stats', __FUNCTION__, $parameter);
    }

    /**
     * 统计用户在线时长
     * @api {get} /stats/userOnline 统计用户时长数据
     * @apiGroup stats
     * @apiName userOnline
     * @ apiParam {String} date 日期
     * @ apiParam {String} server_id 服务器id
     */
    public function userOnline()
    {
        $parameter['start'] = $this->request->query->get('start');
        $parameter['end'] = $this->request->query->get('end');
        $parameter['serverId'] = $this->request->query->get('server_id');
        return $this->api("Stats", __FUNCTION__, $parameter);
    }

    /**
     * 统计玩家通关
     * @api {get} /stats/userPassLevel 统计玩家通关
     * @apiGroup stats
     * @apiName userPassLevel
     * @ apiParam {String} date 日期
     * @ apiParam {String} server_id 服务器id
     */
    public function userPassLevel()
    {
        $parameter['start']    = $this->request->query->get('start');
        $parameter['end']      = $this->request->query->get('end');
        $parameter['serverId'] = $this->request->query->get('server_id');
        return $this->api("Stats", __FUNCTION__, $parameter);
    }

    public function statsLost()
    {
        $parameter['start'] = $this->request->query->get('start');
        $parameter['end'] = $this->request->query->get('end');
        $parameter['lostDay'] = $this->request->query->get('lostDay');
        $parameter['lostcondition'] = $this->request->query->get('lostcondition');
        $parameter['channel'] = $this->request->query->get('channel');
        $parameter['zone'] = $this->request->query->get('zone');
        return $this->api('Stats', __FUNCTION__, $parameter);
    }

    // 实时统计第一个页面
    public function statsTimeQuery()
    {
        $parameter['start'] = $this->request->query->get('start');
        $parameter['end'] = $this->request->query->get('end');
        $parameter['channel'] = $this->request->query->get('channel');
        $parameter['zone'] = $this->request->query->get('zone');
        return $this->api('Stats', __FUNCTION__, $parameter);
    }

    // 实时统计第二个页面，(对比页面)
    public function statsContrast()
    {
        $parameter['start'] = strtotime($this->request->query->get('start'));
        $parameter['end'] = strtotime($this->request->query->get('end'));
        $parameter['channel'] = $this->request->query->get('channel');
        $parameter['zone'] = $this->request->query->get('zone');
        return $this->api('Stats', __FUNCTION__, $parameter);
    }
}