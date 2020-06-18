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
        $response = $this->http($url, 'post', json_encode($pdata));
        $result   = json_decode($response, true);

        // 如果没数据则游戏服务端有问题
        if (count($result) == 0) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $info = $result['data'];
        foreach ($info as $key => $value) {
            // 收到服务端数据后更新活动状态
            // 1: 失败 0: 成功
            if ($value['code'] == 1) {
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
     * @param $method
     * @param $url
     * @param $data
     */
    public function http($url, $method, $postData = NULL)
    {
        $ch = curl_init();

        //curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // 强制访问ipv4地址，如果访问ipv6, namelookup_time时间会过长
        curl_setopt($ch, CURLOPT_HEADER, false);    // 是否返回头信息
        // curl_setopt($ch, CURLOPT_HEADERFUNCTION, array($this, 'getHeader')); //回调


        // set data & url
        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                break;
            case 'GET':
                if (!empty($this->postData)) {
                    $this->url = "{$this->url}?" . http_build_query($postData);
                }
                break;
        }
        curl_setopt($ch, CURLOPT_URL, $url);


        // set headers
        if ($this->headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, []);
            curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
        }

        // execute
        $response = curl_exec($ch);

        curl_close($ch);

        return $response;

    }
}