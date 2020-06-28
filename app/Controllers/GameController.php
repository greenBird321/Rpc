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
}