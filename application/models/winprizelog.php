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
    private $addtime = 0;
    public function init(){
        $this->addtime = date('Y-m-d H:i:s');
    }
    /**
     * 查看是否已经中奖
     * @param $active_id
     * @param $uid
     * @return mixed
     */
    public function checkIsWin($active_id, $uid)
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
        $data['addtime'] = $this->addtime;
        return $this->db->insert($this->table,$data);
    }

}