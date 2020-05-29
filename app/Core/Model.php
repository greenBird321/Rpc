<?php

namespace Xt\Rpc\Core;


class Model
{

    public function __get($name = '')
    {
        global $di;
        return $di[$name];
    }

}