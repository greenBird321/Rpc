<?php

namespace Xt\Rpc\Core;


use Doctrine\DBAL\DriverManager;

class Service
{

    public $di;


    private $db_res;


    public function __construct($di)
    {
        $this->di = $di;
    }


    public function __get($name = '')
    {
        return $this->di[$name];
    }


    protected function gameDb($zone = 0)
    {
        if (!isset($this->db_res[$zone])) {
            if (!isset($this->di['db_cfg'][$zone])) {
                throw new \Exception('no config of the zone');
            }
            $params = array(
                'driver'   => 'pdo_' . $this->di['db_cfg'][$zone]['adapter'],
                'host'     => $this->di['db_cfg'][$zone]['host'],
                'port'     => $this->di['db_cfg'][$zone]['port'],
                'user'     => $this->di['db_cfg'][$zone]['user'],
                'password' => $this->di['db_cfg'][$zone]['pass'],
                'dbname'   => $this->di['db_cfg'][$zone]['db'],
                'charset'  => $this->di['db_cfg'][$zone]['charset'],
            );
            $this->db_res[$zone] = DriverManager::getConnection($params);
        }
        return $this->db_res[$zone];
    }

}