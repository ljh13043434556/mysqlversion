<?php

namespace beck\mysqlvs;

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