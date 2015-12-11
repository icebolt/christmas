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
        $sql = $this->query()->select("*")->from($this->table)->where("id=$id")->build();
        return $ret = $this->db->executeS($sql);
    }

}