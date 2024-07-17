<?php
namespace beck\mysqlvs;

use think\facade\Db;

/**
 * 分析一条要执行的SQL
 */
class SqlCommand extends Unit
{
    protected $sql;
    protected $allowCommand = [
        'create table',
        'alter table',
        'insert into',
        'create index',
        'drop index',
        'update'
    ];


    public function execute($sql)
    {
        $this->sql = $sql;
        if(!$this->analysis()) {
            throw new \Exception('指令不能执行');
        }


        if($this->isCreateTable()) {
            //SQL 为创建 表格 ， 获取table
            $table = $this->getCreateSqlTable();
            if($this->app->table->hasTable($table)) {
                //表已经存在,
                throw new \Exception('table exist');
            }
        } else if($this->isAddColumn()) {

            [$table, $field] = $this->getAlTerColumn();

            if($this->app->field->hasField($table, $field)) {
                //列表已经存在了
                throw new \Exception('column exist');
            }

        }

        //扫行SQL
        return $this->app->db->execute($sql);

    }


    /**
     * 分析当前语句是否可执行
     * @return bool
     */
    public function analysis()
    {

        $sql     = strtolower($this->sql);
        foreach($this->allowCommand as $command)
        {
            if (preg_match("/^{$command}/", $sql)) {
                return true;
            }
        }

        return false;
    }

    public function isCreateTable()
    {
        $sql     = strtolower($this->sql);
        return preg_match("/^create table/", $sql);
    }

    /**
     * 获取创建订单SQL 的表名
     * @return mixed
     * @throws \Exception
     */
    public function getCreateSqlTable()
    {
        $sql     = strtolower($this->sql);
        preg_match("/^create table `(.*?)`/", $sql, $matches);
        if(count($matches) < 2) {
            throw new \Exception('获取表名失败');
        }
        return $this->trim($matches[1]);
    }


    protected function trim($str) {
        return str_replace('`', '', $str);
    }

    /**
     * 获取 alter 中的列名
     * @return mixed
     * @throws \Exception
     */
    public function getAlTerColumn()
    {
        $sql     = strtolower($this->sql);
        preg_match("/^alter table (.*?) add (.*?) /", $sql, $matches);

        if(count($matches) < 3) {
            throw new \Exception('获取列失败');
        }

        return [$this->trim($matches[1]), $this->trim($matches[2])];
    }


    public function isAddColumn()
    {
        $sql     = strtolower($this->sql);
        return preg_match("/^alter table (.*?) add/", $sql);
    }


    public function isDropColumn($sql)
    {
        $sql     = strtolower($sql);
        return preg_match("/^alter table (.*?) drop/", $sql);
    }


}