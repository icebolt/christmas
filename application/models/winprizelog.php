<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/15
 * Time: 上午11:09
 */
class winprizelogModel extends  baseModel
{
    private $table = "winprizelog";
    /**
     * 查看是否已经中奖
     * @param $active_id
     * @param $uid
     * @return mixed
     */
    public function checkWinNum($active_id, $uid)
    {
        $where = "uid =$uid and aid = $active_id";
        $sql = $this->query()->select("count(*) as num")->from($this->table)->where($where)->build();
        return $ret = $this->db->getRow($sql);
    }
    /**
     * 添加中奖人信息
     * @param $data
     * @return mixed
     */
    public function addWin($data){
        return $this->db->insert($this->table,$data);
    }

}
