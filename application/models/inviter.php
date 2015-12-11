<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/10
 * Time: 下午6:25
 */
class inviterModel extends BaseModel
{
    private $table = "inviter";

    /**
     * 获取好友
     * @param $uid
     * @return mixed
     */
    public function getInviter($uid)
    {
        $sql = $this->query()->from($this->table)->where("inviter_id = $uid")->limit(6)->build();
        return $this->db->executeS($sql);
    }
}