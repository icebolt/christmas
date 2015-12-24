<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/10
 * Time: 上午11:57
 */
class activeModel extends baseModel
{
    private $table = "active";
    public function getActive($id)
    {
        $time = date('Y-m-d',time());
        $where = "id = $id and starttime < '{$time}' and endtime > '{$time}' and `status` =0 ";
        $sql = $this->query()->select("*")->from($this->table)->where($where)->build();
        return $ret = $this->db->getRow($sql);
    }

}