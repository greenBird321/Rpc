<?php
/**
 * 活动相关
 * Class ActivityService
 */

namespace Xt\Rpc\Services\ML_lmyz;

use Xt\Rpc\Models\Activity;
use Exception;
class ActivityService extends \Xt\Rpc\Services\XT_app\ActivityService
{
    private $activityModel;

    public function __construct($di)
    {
        parent::__construct($di);
        $this->activityModel = new Activity();
    }

    public function test($parameter)
    {
        return [
            'code' => 0,
            'msg' => $parameter['msg']
        ];
    }

    /**
     * 活动保存并上报服务端
     * @return int
     */
    public function import($parameter)
    {
        $data['title']       = $parameter['title'];
        $data['content']     = $parameter['content'];
        $data['zone']        = $parameter['zone'];
        $data['create_time'] = date('Y-m-d H:i:s', time());
        $url                 = $this->di['db_cfg']['game_url']['award_url'];
        try {
            $zones = $this->activityModel->saveActivity($data);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $pdata = [
            'code' => 0,
            'msg' => 'success',
            'zones' => $zones,
            'data' => [
                'title' => $data['title'],
                'content' => base64_encode($data['content'])
            ]
        ];

        // 数据发送
        $response = $this->post($url, json_encode($pdata));
        $result   = json_decode($response, true);

        // 如果没数据则游戏服务端有问题
        if (empty($result)) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $info = $result['data'];
        foreach ($info as $key => $value) {
            // 收到服务端数据后更新活动状态
            // 1: 失败 0: 成功
            if ($value['code'] != 0) {
                $this->activityModel->updateStatus($value['id']);
            }
        }

        return ['code' => 0, 'msg' => 'success'];
    }

    /**
     * 服务端主动拉取活动
     */
    public function game($parameter)
    {
        $result = $this->activityModel->getActivityList($parameter['zone']);
        
        if (empty($result)) {
            return ['code' => 1, 'msg' => 'failed'];
        }

        $data = [];
        foreach ($result as $key => $value) {
            $data[] = [
                'title' => $value['title'],
                'content' => base64_encode($value['content'])
            ];
        }
        return [
            'code' => 0,
            'msg' => 'success',
            'zone' => $parameter['zone'],
            'data' => $data
        ];
    }

    /**
     * ML活动列表
     * @param $parameter
     */
    public function ml_lists($parameter)
    {
        try {
            $data = $this->activityModel->getMLActivityList($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }
        return array_merge(
            [
                'code' => 0,
                'msg'  => 'success'
            ],
            $data
        );
    }

    /**
     * 发送请求
     * @param $url
     * @param $data
     */
    public function post($url, $data) {

        //初使化init方法
        $ch = curl_init();

        //指定URL
        curl_setopt($ch, CURLOPT_URL, $url);

        //设定请求后返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //声明使用POST方式来进行发送
        curl_setopt($ch, CURLOPT_POST, 1);

        //发送什么数据呢
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


        //忽略证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //忽略header头信息
        curl_setopt($ch, CURLOPT_HEADER, 0);

        //设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        //发送请求
        $output = curl_exec($ch);

        //关闭curl
        curl_close($ch);

        //返回数据
        return $output;
    }
}