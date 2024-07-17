<?php

namespace mysqlversion;

use think\facade\Db;

/**
 * @property MysqlTable $table
 * @property MysqlFields $field
 * @property VersionDb $db
 * @property SqlCommand $sqlCommand
 * @property Init $init
 * @property Client $client
 */
class MysqlVersion
{
    protected $bind = [
        'table'      => MysqlTable::class,
        'field'      => MysqlFields::class,
        'db'         => VersionDb::class,
        'sqlCommand' => SqlCommand::class,
        'init'       => Init::class,
        'client'     => Client::class
    ];

    protected $unit = [
    ];


    public function __construct()
    {
        $this->init->init();
    }


    public function __get(string $name)
    {
        if (!isset($this->bind[$name])) {
            new \Exception('组件未定义');
        }


        if (isset($this->unit[$name])) {
            return $this->unit[$name];
        }

        $class = $this->bind[$name];

        $this->unit[$name] = (new $class($this));

        return $this->unit[$name];
    }


    public function update()
    {
        $num = 0;

        do{
            $id = $this->getLastId();
            $result = $this->client->getList($id);

            if(isset($result['code']) && $result['code'] == 0)
            {
                $list = $result['data']['list'];
                if(empty($list)) {
                    die("更新完成,更新{$num}条SQL");
                }

                try{
                    foreach($list as $item) {
                        try{
                            //扫行SQL
                            $this->sqlCommand->execute($item['content']);
                            $item['execute_result'] = 1;
                            $item['result_note'] = 'ok';
                            $this->addLog($item);
                            $num++;
                        }catch (\Exception $e) {
                            $msg = $e->getMessage();
                            switch ($msg)
                            {
                                case 'table exist':
                                    //跳过
                                    $item['execute_result'] = 3;
                                    $item['result_note'] = '表格已存在,跳过';
                                    $this->addLog($item);
                                    break;
                                case 'column exist':
                                    //跳过
                                    $item['execute_result'] = 3;
                                    $item['result_note'] = '字段已存在,跳过';
                                    $this->addLog($item);
                                    break;
                                default:
                                    //出错了,中断执行
                                    throw $e;
                            }
                        }
                    }
                }catch (\Exception $e) {
                    var_dump("更新完成,更新{$num}条SQL");
                    die($e->getMessage());
                }
            }
            else
            {
                //未知道的错误
                die($result['msg'] ?? '获取更新数据，未知道错误');
            }
        }while(true);

    }


    protected function addLog($param) {

        $param['execute_time'] = date('Y-m-d H:i:s');
        $sql = "insert into ljh_mysql_version_control(id,content,note,author,create_time,execute_result,result_note)
 values (\"{$param['id']}\",\"{$param['id']}\",\"{$param['content']}\",\"{$param['author']}\",\"{$param['create_time']}\",\"{$param['execute_result']}\",\"{$param['result_note']}\")";

        $this->db->execute($sql);
    }


    protected function getLastId()
    {
        $result = $this->db->query('select id from ljh_mysql_version_control order by id DESC limit 1');
        $id = 0;
        if(!empty($result)) {
            return $result[0]['id'];
        }
    }
}