<?php

namespace Xt\Rpc\Core;


class Controller
{

    public $di;

    private $request;

    private $response;


    public function __construct($di)
    {
        $this->di = $di;
    }


    public function __get($name = '')
    {
        return $this->di[$name];
    }

}