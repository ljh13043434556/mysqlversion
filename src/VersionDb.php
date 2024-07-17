<?php

namespace beck\mysqlvs;


class VersionDb extends Unit
{
    protected function isThinnk()
    {
        return class_exists('\think\facade\Db');
    }

    public function isYii() {
        return class_exists('\Yii');
    }

    function query($sql) {
        if($this->isThinnk()) {
            return \think\facade\Db::query($sql);
        } else if($this->isYii()) {
            return \Yii::$app->db->createCommand($sql)
                ->queryAll();
        }
    }


    public function execute($sql)
    {
        if($this->isThinnk()) {
            return \think\facade\Db::execute($sql);
        } else if($this->isYii()) {
            return \Yii::$app->db->createCommand($sql)
                ->execute();
        }
    }
}