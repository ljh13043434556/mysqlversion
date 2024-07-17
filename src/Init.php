<?php
namespace mysqlversion;

use think\facade\Db;

/**
 * 初始化环境
 */
class Init extends Unit
{
    public function init()
    {
        //没有这个表创建这个表
        if(!$this->app->table->hasTable('ljh_mysql_version_control')) {
            $this->app->db->execute("CREATE TABLE `ljh_mysql_version_control` (
                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `content` text NOT NULL COMMENT 'SQ要执行的SQL',
                  `note` varchar(255) NOT NULL DEFAULT '' COMMENT '说明',
                  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '写SQL的人',
                  `create_time` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
                  `execute_result` tinyint(3) NOT NULL DEFAULT '0' COMMENT '1执行成功   2执行失败',
                  `execute_time` datetime DEFAULT NULL COMMENT '执行时间',
                  `result_note` varchar(100) NOT NULL DEFAULT '' COMMENT '执行说明',
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }
    }
}