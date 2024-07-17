<?php
namespace mysqlversion;

use think\facade\Db;
class MysqlTable extends Unit
{
    protected $tables = [];

    public function getAllTable()
    {
        $result = $this->app->db->query('SHOW TABLES;');

        $tables = [];
        foreach($result as $item) {
            $tables[] = current($item);
        }

        $this->tables = $tables;
        return $this->tables;

    }


    /**
     * 检查表是否存在
     * @param $talbe
     * @return bool
     */
    public function hasTable($talbe)
    {
        $talbes = $this->getAllTable();
        return in_array($talbe, $talbes);
    }
}