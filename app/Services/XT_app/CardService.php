<?php

/**
 * 礼品卡
 * Class CardService
 */
namespace Xt\Rpc\Services\XT_app;


use Xt\Rpc\Core\Service;
use Xt\Rpc\Models\Card;
use Exception;

class CardService extends Service
{

    private $cardModel;

    public function __construct($di)
    {
        parent::__construct($di);
        $this->cardModel = new Card();
    }


    public function lists($parameter = [])
    {
        try {
            $data = $this->cardModel->lists($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }


    public function item($parameter = [])
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $data = $this->cardModel->item($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }


    public function create($parameter = [])
    {
        if (empty($parameter['count'])
            || empty($parameter['type'])
            || empty($parameter['data'])
            || empty($parameter['title'])
            || empty($parameter['expired_in']
                || empty($parameter['code_limit_times']))
        ) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->cardModel->create($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function modify($parameter = [])
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->cardModel->modify($parameter);
        } catch (Exception $e) {
            return [
                'code' => 1,
                'msg' => $e->getMessage()
            ];
        }

        return ['code' => 0, 'msg' => 'success'];
    }


    public function remove($parameter = [])
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $this->cardModel->remove($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return ['code' => 0, 'msg' => 'success'];
    }

    public function search($parameter = [])
    {
        if (empty($parameter['role_id']) && empty($parameter['topic_id']) && empty($parameter['app_id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $data = $this->cardModel->search($parameter);
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return array_merge(['code' => 0, 'msg' => 'success'], $data);
    }

    public function download($parameter = [])
    {
        if (empty($parameter['id'])) {
            return ['code' => 1, 'msg' => 'missing parameter'];
        }

        try {
            $data = $this->cardModel->download($parameter['id']);
            $this->export_csv($data, ['礼包码', '状态'], 'code' . '_' . date('Y-m-d', time()));
        } catch (Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }

        return ['code' => 0, 'msg' => 'success'];
    }

    /**
     * @param $data: mysql数据，二维数组，并且内部数组需要以自然键
     * @param $title_arr: csv 表头
     * @param string $file_name
     */
    function export_csv($data = [],$title_arr = [],$file_name=''){
        if (!is_array($data) && !is_array($title_arr)) {
            throw new Exception('export_csv传入的数据类型不正确');
        }

        ini_set("max_execution_time", "3600");

        $csv_data = '';

        /** 标题 */
        $nums = count($title_arr);

        for ($i = 0; $i < $nums - 1; ++$i) {
            //$csv_data .= '"' . $title_arr[$i] . '",';
            $csv_data .= $title_arr[$i] . ',';
        }
        if ($nums > 0) {
            $csv_data .= $title_arr[$nums - 1] . "\r\n";
        }

        foreach ($data as $k => $row) {
            $_tmp_csv_data = '';
            foreach ($row as $key => $r){
                $row[$key] = str_replace("\"", "\"\"", $r);

                if ( $_tmp_csv_data == '' ) {
                    $_tmp_csv_data = $row[$key];
                }
                else {
                    $_tmp_csv_data .= ','. $row[$key];
                }

            }

            $csv_data .= $_tmp_csv_data . "\r\n";
            unset($data[$k]);
        }

        $csv_data = mb_convert_encoding($csv_data, "cp936", "UTF-8");
        $file_name = empty($file_name) ? date('Y-m-d-H-i-s', time()) : $file_name;
        // 解决IE浏览器输出中文名乱码的bug
        if(preg_match( '/MSIE/i', $_SERVER['HTTP_USER_AGENT'] )){
            $file_name = urlencode($file_name);
            $file_name = iconv('UTF-8', 'GBK//IGNORE', $file_name);
        }
        $file_name = $file_name . '.csv';
        header('Content-Type: application/download');
        header("Content-type:text/csv;");
        header("Content-Disposition:attachment;filename=" . $file_name);
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo $csv_data;
        exit();
    }
}