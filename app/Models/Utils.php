<?php

namespace Xt\Rpc\Models;


use Xt\Rpc\Core\Model;
use DateTime;
use DateTimeZone;

class Utils extends Model
{

    /**
     * 切换时区
     * @param string $timeZone
     * @param string $fromTimeZone
     * @param string $datetime
     * @param string $format
     * @return string
     */
    public function switchTimeZone($timeZone = '', $fromTimeZone = 'UTC', $datetime = '', $format = 'Y-m-d H:i:s')
    {
        $date = new DateTime($datetime, new DateTimeZone($fromTimeZone));
        $date->setTimezone(new DateTimeZone($timeZone));
        return $date->format($format);
    }


    /**
     * 生成36进制随机字符串
     * @param bool|true $more_entropy
     * @return string
     */
    public function genString($more_entropy = true)
    {
        $s = uniqid('', $more_entropy);
        if (!$more_entropy) {
            return base_convert($s, 16, 36);
        }
        $hex = substr($s, 0, 13);
        $dec = $s[13] . substr($s, 15);
        return base_convert($hex, 16, 36) . base_convert($dec, 10, 36);
    }

}