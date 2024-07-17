<?php
namespace mysqlversion;

use think\facade\Db;

class MysqlFields extends Unit
{

    public function getTableFields($table)
    {
        $result = Db::query("SHOW COLUMNS FROM {$table};");
        return $result;
    }


    /**
     * 判断 表中是否包含了某个字段
     * @param $name
     * @return bool
     */
    public function hasField($table, $name)
    {
        $result = $this->getTableFields($table);
        foreach($result as $field)
        {
            if($field['Field'] == $name) {
                return true;
            }
        }

        return false;
    }
}