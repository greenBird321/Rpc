<?php

namespace Xt\Rpc\Controllers;


use Xt\Rpc\Models\Trade;

class TradeController extends ControllerBase
{

    /**
     * @api {get} /trade/purchase 充值接口purchase
     * @apiDescription 特殊说明: Http协议请求此接口成功返回的字符串success, Soap与Yar协议返回数组
     * @apiName purchase
     * @apiGroup trade
     *
     * @apiParam {String} transaction 订单ID
     * @apiParam {String} gateway 充值网关
     * @apiParam {String} amount 金额(单位元)
     * @apiParam {String} currency 币种
     * @apiParam {String} product_id 产品ID
     * @apiParam {String} user_id 账号ID
     * @apiParam {String} timestamp 时间戳
     * @apiParam {String} sign 签名
     *
     * @apiSuccess {Number} code 返回状态
     * @apiSuccess {String} msg 返回消息
     *
     * @apiSuccessExample SOAP & YAR Response:
     *     HTTP/1.1 200 OK
     *     ['code'=>0, 'msg'=>'success']
     *
     * @apiSuccessExample  HTTP Response:
     *     HTTP/1.1 200 OK
     *     success
     */
    public function purchase()
    {
        $user_id = $this->request->get('user_id');
        $custom = $this->request->get('custom');

        // 补全设备信息 TODO :: 暂无索引 (可通过Trade透传，但需考虑安全为题)
        $tradeModel = new Trade();
        $device = $tradeModel->getDevice($user_id);
        if ($device) {
            $parameter['uuid'] = $device['uuid'];
            $parameter['adid'] = $device['adid'];
            $parameter['device'] = $device['device'];
            $parameter['ip'] = $device['ip'];
        }

        $parameter['transaction'] = $this->request->get('transaction');
        $parameter['gateway'] = $this->request->get('gateway');
        $parameter['amount'] = $this->request->get('amount');
        $parameter['currency'] = $this->request->get('currency');
        $parameter['product_id'] = $this->request->get('product_id');
        $parameter['timestamp'] = $this->request->get('timestamp');
        $parameter['sign'] = $this->request->get('sign');
        $parameter['user_id'] = empty($custom) ? $user_id : $custom;
        $parameter['ext'] = $this->request->get('ext');
        $response = $this->api('Trade', 'purchase', $parameter);
        if (!$response) {
            exit('failed, no response');
        }
        if ($response['code'] != 0) {
            exit($response['msg']);
        }
        exit('success');
    }

}