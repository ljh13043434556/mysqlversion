<?php

namespace mysqlversion;

use think\facade\Db;

class Unit
{
    /**
     * @var MysqlVersion
     */
    protected $app;
    public function __construct($app)
    {
        $this->app = $app;
    }

}