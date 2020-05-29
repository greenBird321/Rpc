<?php

namespace Xt\Rpc\Controllers;


use Xt\Rpc\Core\Controller;

class ControllerBase extends Controller
{

    public function api($api = '', $method = '', $parameter = [])
    {
        $class = '\\Xt\\Rpc\\Services\\' . $this->di['route']['app'] . '\\' . ucfirst($api) . 'Service';
        $controller = new $class($this->di);
        return $controller->$method($parameter);
    }

}