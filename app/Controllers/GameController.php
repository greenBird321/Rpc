<?php
/**
 * 游戏相关.
 * User: lihe
 * Date: 2020/6/22
 * Time: 8:09 PM
 */

namespace Xt\Rpc\Controllers;

class GameController extends ControllerBase
{
    /**
     * 查询消费钻石排行
     * @param $parameter
     */
    public function getConsumeList()
    {
        $parameter['serverId'] = $this->request->query->get('serverId');
        $parameter['start']    = $this->request->query->get('start');
        $parameter['end']      = $this->request->query->get('end');

        return $this->api('Game', __FUNCTION__, $parameter);
    }

    /**
     * 商城道具消费排行
     */
    public function shopRanking()
    {
        $parameter['serverId'] = $this->request->query->get('zone');
        $parameter['start']    = $this->request->query->get('start');
        $parameter['end']      = $this->request->query->get('end');

        return $this->api('Game', __FUNCTION__, $parameter);
    }

    /**
     * 小时内创建用户数
     */
    public function userCreateTimes()
    {
        $parameter['serverId'] = $this->request->query->get('zone');
        $parameter['start']    = $this->request->query->get('start');
        $parameter['end']      = $this->request->query->get('end');

        return $this->api('Game', __FUNCTION__, $parameter);
    }

    /**
     * 获取白名单
     */
    public function getWhiteList()
    {
        return $this->api('Game', __FUNCTION__);
    }

    /**
     * 增加白名单
     */
    public function addWhiteList()
    {
        $parameter['ip'] = $this->request->query->get('ip');
        $parameter['flag'] = $this->request->query->get('flag');

        return $this->api('Game', __FUNCTION__, $parameter);
    }

    /**
     * 删除白名单
     */
    public function deleteWhiteList()
    {
        $parameter['id'] = $this->request->query->get('id');

        return $this->api('Game', __FUNCTION__, $parameter);
    }

    /**
     * Bi查询实时数据(不保存数据)
     */
    public function realTimeData()
    {
        $parameter['zone'] = $this->request->query->get('zone');
        return $this->api('Game', __FUNCTION__, $parameter);
    }

    /**
     * 充值排行 每个app 都需要修改
     */
    public function top()
    {
        $parameter['zone']    = $this->request->query->get('zone');
        $parameter['start']   = empty($this->request->query->get('start')) ? '' : date('Y-m-d H:i:s', $this->request->query->get('start'));
        $parameter['end']     = empty($this->request->query->get('end')) ? '' : date('Y-m-d H:i:s', $this->request->query->get('end'));
        $parameter['channel'] = $this->request->query->get('channel');
        return $this->api('Game', __FUNCTION__, $parameter);
    }

    /**
     * 首冲分布
     */
    public function distribution()
    {
        $parameter['zone']  = $this->request->query->get('zone');
        $parameter['start'] = empty($this->request->query->get('start')) ? '' : date('Y-m-d H:i:s', $this->request->query->get('start'));
        $parameter['end']   = empty($this->request->query->get('end')) ? '' : date('Y-m-d H:i:s', $this->request->query->get('end'));
        return $this->api('Game', __FUNCTION__, $parameter);
    }

    /**
     * 充值分布(宏观数据)
     */
    public function rechargeDistribution()
    {
        $parameter['zone']  = $this->request->query->get('zone');
        $parameter['start'] = empty($this->request->query->get('info_start')) ? '' : date('Y-m-d H:i:s', $this->request->query->get('info_start'));
        $parameter['end']   = empty($this->request->query->get('info_end')) ? '' : date('Y-m-d H:i:s', $this->request->query->get('info_end'));
        return $this->api('Game', __FUNCTION__, $parameter);
    }

    /**
     * 充值分布(微观数据)
     */
//    public function rechargeDistributionInfo()
//    {
//        $parameter['zone'] = $this->request->query->get('zone');
//        $parameter['start'] = empty($this->request->query->get('start')) ? '' : date('Y-m-d H:i:s', $this->request->query->get('start'));
//        $parameter['end']   = empty($this->request->query->get('end')) ? '' : date('Y-m-d H:i:s', $this->request->query->get('end'));
//        return $this->api('Game', __FUNCTION__, $parameter);
//    }

    /**
     * 向游戏服务端发送gm指令
     */
    public function sendGmData()
    {
        $parameter['server'] = $this->request->query->get('server');
        $parameter['gm'] = $this->request->query->get('gm');
        return $this->api('Game', __FUNCTION__, $parameter);
    }

    /**
     * 流失情况
     */
    public function lostPlayer()
    {
        $parameter['start']  = $this->request->query->get('start');
        $parameter['end']    = $this->request->query->get('end');
        $parameter['server'] = $this->request->query->get('zone');

        return $this->api('Game', __FUNCTION__, $parameter);
    }
}