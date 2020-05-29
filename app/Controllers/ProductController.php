<?php

namespace Xt\Rpc\Controllers;


class ProductController extends ControllerBase
{

    /**
     * 产品列表
     * @api {get} /product/lists 产品列表lists
     * @apiGroup product
     * @apiName lists
     *
     * @apiParam {String} [gateway] 产品网关 例:apple,alipay
     *
     * @apiSuccess {Number} product_id 产品ID
     * @apiSuccess {String} gateway 网关
     * @apiSuccess {Number} price 价格
     * @apiSuccess {String} currency 货币种类
     * @apiSuccess {Number} coin 货币数量
     * @apiSuccess {String} custom 自定义 例:card_month
     * @apiSuccess {Number} status=1 状态,1正常
     * @apiSuccess {Number} sort=0 排序
     * @apiSuccess {String} name 名称
     * @apiSuccess {String} remark 备注
     * @apiSuccess {String} image 产品图片URL
     * @apiSuccess {String} package 软件包
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "count": "2",
     *        "data": [
     *            {
     *                "product_id": "hnmj.diamond.6",
     *                "gateway": "apple",
     *                "price": "2.00",
     *                "currency": "USD",
     *                "coin": "2",
     *                "custom": "",
     *                "status": "1",
     *                "sort": "0",
     *                "name": "2钻石",
     *                "remark": "",
     *                "image": "",
     *                "package": ""
     *            },
     *            {
     *                "product_id": "gt.king.pirate.tw.012",
     *                "gateway": "apple",
     *                "price": "350.00",
     *                "currency": "USD",
     *                "coin": "440",
     *                "custom": "",
     *                "status": "1",
     *                "sort": "0",
     *                "name": "440钻石",
     *                "remark": "440钻石",
     *                "image": "",
     *                "package": ""
     *            }
     *        ]
     *    }
     */
    public function lists()
    {
        $parameter['gateway'] = $this->request->query->get('gateway');
        return $this->api('Product', __FUNCTION__, $parameter);
    }


    /**
     * 产品详细
     * @api {get} /product/item 产品详细item
     * @apiGroup product
     * @apiName item
     *
     * @apiParam {String} product_id 产品ID
     *
     * @apiSuccess {Number} product_id 产品ID
     * @apiSuccess {String} gateway 网关
     * @apiSuccess {Number} price 价格
     * @apiSuccess {String} currency 货币种类
     * @apiSuccess {Number} coin 货币数量
     * @apiSuccess {String} custom 自定义
     * @apiSuccess {Number} status=1 状态,1正常
     * @apiSuccess {Number} sort=0 排序
     * @apiSuccess {String} name 名称
     * @apiSuccess {String} remark 备注
     * @apiSuccess {String} image 产品图片URL
     * @apiSuccess {String} package 软件包
     *
     * @apiSuccessExample Success-Response:
     *    HTTP/1.1 200 OK
     *    {
     *        "code": 0,
     *        "msg": "success",
     *        "data": {
     *            "product_id": "gt.king.pirate.tw.017",
     *            "gateway": "apple",
     *            "price": "1150.00",
     *            "currency": "CNY",
     *            "coin": "1520",
     *            "custom": "",
     *            "status": "1",
     *            "sort": "0",
     *            "name": "1520钻石",
     *            "remark": "首充赠送100钻石,活动赠送10钻石",
     *            "image": "",
     *            "package": "",
     *            "more": [
     *                {
     *                    "id": "110",
     *                    "product_id": "gt.king.pirate.tw.017",
     *                    "type": "first_purchase",
     *                    "lowest": "0",
     *                    "coin": "100",
     *                    "prop": "",
     *                    "start_time": "2017-01-01 00:00:00",
     *                    "end_time": "2017-10-01 00:00:00"
     *                },
     *                {
     *                    "id": "111",
     *                    "product_id": "gt.king.pirate.tw.017",
     *                    "type": "promo",
     *                    "lowest": "0",
     *                    "coin": "10",
     *                    "prop": "",
     *                    "start_time": "2017-07-01 00:00:00",
     *                    "end_time": "2017-08-01 00:00:00"
     *                }
     *            ]
     *        }
     *    }*
     */
    public function item()
    {
        $parameter['product_id'] = $this->request->query->get('product_id');
        return $this->api('Product', __FUNCTION__, $parameter);
    }


    /**
     * 产品创建
     * @api {get} /product/create 产品创建create
     * @apiGroup product
     * @apiName create
     *
     * @apiParam {Number} product_id 产品ID
     * @apiParam {String} gateway 网关
     * @apiParam {Number} price 价格
     * @apiParam {String} currency 货币种类
     * @apiParam {Number} coin 货币数量
     * @apiParam {String} [custom] 自定义 例:card_month
     * @apiParam {Number} [status=1] 状态,1正常
     * @apiParam {Number} [sort=0] 排序
     * @apiParam {String} name 名称
     * @apiParam {String} remark 备注
     * @apiParam {String} [image] 产品图片URL
     * @apiParam {String} [package] 软件包
     */
    public function create()
    {
        $parameter = [
            'product_id' => $this->request->query->get('product_id'),
            'gateway'    => $this->request->query->get('gateway'),
            'price'      => $this->request->query->get('price'),
            'currency'   => $this->request->query->get('currency'),
            'coin'       => $this->request->query->get('coin'),
            'custom'     => $this->request->query->get('custom'),
            'status'     => $this->request->query->get('status'),
            'sort'       => $this->request->query->get('sort'),
            'name'       => $this->request->query->get('name'),
            'remark'     => $this->request->query->get('remark'),
            'image'      => $this->request->query->get('image'),
            'package'    => $this->request->query->get('package'),
        ];
        return $this->api('Product', __FUNCTION__, $parameter);
    }


    /**
     * 产品修改
     * @api {get} /product/modify 产品修改modify
     * @apiGroup product
     * @apiName modify
     *
     * @apiParam {Number} product_id 产品ID
     * @apiParam {String} [gateway] 网关
     * @apiParam {Number} [price] 价格
     * @apiParam {String} [currency] 货币种类
     * @apiParam {Number} [coin] 货币数量
     * @apiParam {String} [custom] 自定义 例:card_month
     * @apiParam {Number} [status=1] 状态,1正常
     * @apiParam {Number} [sort=0] 排序
     * @apiParam {String} [name] 名称
     * @apiParam {String} [remark] 备注
     * @apiParam {String} [image] 产品图片URL
     * @apiParam {String} [package] 软件包
     */
    public function modify()
    {
        $parameter = [
            'product_id' => $this->request->query->get('product_id'),
            'gateway'    => $this->request->query->get('gateway'),
            'price'      => $this->request->query->get('price'),
            'currency'   => $this->request->query->get('currency'),
            'coin'       => $this->request->query->get('coin'),
            'custom'     => $this->request->query->get('custom'),
            'status'     => $this->request->query->get('status'),
            'sort'       => $this->request->query->get('sort'),
            'name'       => $this->request->query->get('name'),
            'remark'     => $this->request->query->get('remark'),
            'image'      => $this->request->query->get('image'),
            'package'    => $this->request->query->get('package'),
        ];
        return $this->api('Product', __FUNCTION__, $parameter);
    }


    /**
     * 产品删除
     * @api {get} /product/remove 产品删除remove
     * @apiGroup product
     * @apiName remove
     *
     * @apiParam {String} [product_id] 产品ID
     *
     */
    public function remove()
    {
        $parameter['product_id'] = $this->request->query->get('product_id');
        return $this->api('Product', __FUNCTION__, $parameter);
    }


    /**
     * 产品选项配置创建
     * @api {get} /product/create_option 产品选项配置创建create_option
     * @apiGroup product
     * @apiName create_option
     *
     * @apiParam {String} product_id 产品ID
     * @apiParam {String} type 选项类型 promo,first_purchase
     * @apiParam {Number} [lowest] 低限
     * @apiParam {Number} [coin] 货币数量
     * @apiParam {String} [prop] 道具
     * @apiParam {String} start_time 开始时间
     * @apiParam {String} end_time 结束时间
     */
    public function create_option()
    {
        $parameter['product_id'] = $this->request->query->get('product_id');
        $parameter['type'] = $this->request->query->get('type');
        $parameter['lowest'] = $this->request->query->get('lowest');
        $parameter['coin'] = $this->request->query->get('coin');
        $parameter['prop'] = $this->request->query->get('prop');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        return $this->api('Product', __FUNCTION__, $parameter);
    }


    /**
     * 产品选项配置修改
     * @api {get} /product/modify_option 产品选项配置删除modify_option
     * @apiGroup product
     * @apiName modify_option
     *
     * @apiParam {String} id 产品选项配置ID
     * @apiParam {String} [type] 选项类型 promo,first_purchase
     * @apiParam {Number} [lowest] 低限
     * @apiParam {Number} [coin] 货币数量
     * @apiParam {String} [prop] 道具
     * @apiParam {String} [start_time] 开始时间
     * @apiParam {String} [end_time] 结束时间
     */
    public function modify_option()
    {
        $parameter['id'] = $this->request->query->get('id');
        $parameter['type'] = $this->request->query->get('type');
        $parameter['lowest'] = $this->request->query->get('lowest');
        $parameter['coin'] = $this->request->query->get('coin');
        $parameter['prop'] = $this->request->query->get('prop');
        $parameter['start_time'] = $this->request->query->get('start_time');
        $parameter['end_time'] = $this->request->query->get('end_time');
        return $this->api('Product', __FUNCTION__, $parameter);
    }


    /**
     * 产品选项配置删除
     * @api {get} /product/remove_option 产品选项配置删除remove_option
     * @apiGroup product
     * @apiName remove_option
     *
     * @apiParam {String} id 产品选项配置ID
     */
    public function remove_option()
    {
        $parameter['id'] = $this->request->query->get('id');
        return $this->api('Product', __FUNCTION__, $parameter);
    }

}