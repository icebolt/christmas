<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/22
 * Time: 下午4:39
 */
class prizeLevelModel extends baseModel
{
    private $table = "prize_level";

    public function getList($active_id, $type){
        $where = "type='{$type}' AND active_id = {$active_id}";
        echo $sql = $this->query()->select('*')->from($this->table)->where($where)->build();
        return $this->db->executeS($sql);
    }
}