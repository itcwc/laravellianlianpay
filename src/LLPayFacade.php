<?php

namespace  Itcwc\LianLianPay;

use Illuminate\Support\Facades\Facade;

class LLPayFacade extends Facade
{
    /**
     * 获取组件的注册名称。
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Uploader::class;
    }
}