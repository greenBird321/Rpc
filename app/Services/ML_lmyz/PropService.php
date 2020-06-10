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

        $award_url = $parameter['award_url'];

        $server_id = $parameter['zone'];
        $title     = $parameter['title'];
        $msg       = $parameter['msg'];
        $send      = $parameter['attach'];

        $award_param = json_encode([
            'zone' => $server_id,
            'data' => $send,
            'title' => $title,
            'content' => $msg
        ]);
        // 请求服务端api
        $result = $this->CurlGame('post', $award_url, $award_param);
        if ($result['code'] == 0) {
            return ['code' => 0, 'msg' => 'success'];
        } else {
            return ['code' => 1, 'msg' => 'failed'];
        }
    }

    /**
     * 发送Http请求
     * @param $method
     * @param $url
     * @param $param
     * @return mixed
     */
    protected function CurlGame($method, $url, $param)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); // 强制访问ipv4地址，如果访问ipv6, namelookup_time时间会过长
        curl_setopt($ch, CURLOPT_HEADER, false);    // 是否返回头信息

        switch (strtolower($method)) {
            case 'get':
                $url .= '?' . http_build_query($param);
                break;
            case 'post':
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
                break;
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}