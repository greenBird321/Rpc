<?php

/**
 * 道具相关
 * Class PropService
 */
namespace Xt\Rpc\Services\ML_lmyz;


class PropService extends \Xt\Rpc\Services\HT_haizei\PropService
{
    public function coin($parameter)
    {
        return ['code' => 1, 'msg' => 'failed'];
    }

    public function attach($parameter)
    {
        if (empty($parameter['zone']) || empty($parameter['user_id']) || empty($parameter['attach'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }
        $zone = $parameter['zone'];
        $user_id = $parameter['user_id'];
        $msg = empty($parameter['msg']) ? '' : $parameter['msg'];

        return ['code' => 0, 'msg' => 'success'];
    }

    /**
     * 群发邮件
     * @param $parameter
     * @return array
     */
    public function attachServer($parameter)
    {
        if (empty($parameter['zone']) || empty($parameter['attach'])) {
            return ['code' => 1, 'msg' => 'missing paraneter'];
        }

        $award_url = $this->di['db_cfg']['game_url']['award_url'];
        $server = $parameter['zone'];
        $title     = $parameter['title'];
        $content   = $parameter['msg'];
        $prop      = $parameter['attach'];

        if (strpos($prop, ',')) {
            $prop = explode(',', $prop);
        }

        if (strpos($server, ',')) {
            $server = explode(',', $server);
        }

        $award_param = [
            'data' => [
                'serverId' => $server,
                'prop' => $prop,
                'title' => $title,
                'content' => $content
            ],
        ];
        dump($award_param);exit;
        // 请求服务端api
        $response = $this->post($award_url, json_encode($award_param, true));
        if (json_decode($response['code']) == 0) {
            return ['code' => 0, 'msg' => 'success'];
        } else {
            return ['code' => 1, 'msg' => 'failed'];
        }
    }

    // 单用户发邮件 type = 1; 区服邮件 type = 2
    public function mail($parameter)
    {
        if (empty($parameter['zone']) && empty($parameter['user_id']) && empty($parameter['amount'])) {
            return [
                'code' => 1,
                'msg' => 'failed'
            ];
        }

        $title = $parameter['title'];
        $content = $parameter['msg'];
        $user_id = $parameter['user_id'];
        $prop = $parameter['amount'];
        $server = $parameter['zone'];
        $award_url = $this->di['db_cfg']['game_url']['award_url'];

        if (strpos($user_id, ',')) {
            $user_id = explode(',', $user_id);
        }

        if (strpos($prop, ',')) {
            $prop = explode(',', $prop);
        }

        $award_param = [
            'data' => [
                'serverId' => $server,
                'roleId' => $user_id,
                'prop' => $prop,
                'title' => $title,
                'content' => $content,
                'type' => 1
            ],
        ];
        dump($award_param);exit;
        $response = $this->post($award_url, json_encode($award_param, true));
        if (json_decode($response['code']) == 0) {
            return ['code' => 0, 'mgs' => 'success'];
        } else {
            return ['code' => 1, 'msg' => 'failed'];
        }
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