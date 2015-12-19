<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/10
 * Time: 上午11:57
 */
class activeUserModel extends baseModel
{
    private $table = "active_user";
    private $open_id = '';
    private $active_id = 0;
    private $type = 0;
    private $inviter_id = 0;
    private $addtime = '';
    private $rand_string = '';
    public function __construct(){
        parent::__construct();
        $this->addtime = date('Y-m-d H:i:s',time());
        $this->randNum();
    }

    /**
     * 获取用户信息登录
     * @param $active_id 活动ID
     * @param $type      类型
     * @param $open_id   openid
     * @return mixed
     */
    public function getUser($active_id, $type, $open_id)
    {
        $where = "open_id ='{$open_id}' and active_id = $active_id and type = $type";
        $sql = $this->query()->select("*")->from($this->table)->where($where)->build();
        return $ret = $this->db->getRow($sql);
    }

    public function getUserInfo($uid)
    {
        $sql = $this->query()->select("*")->from($this->table)->where("id = $uid")->build();
        return $ret = $this->db->getRow($sql);
    }

    /**
     * 添加用户
     * @param $data
     * @return mixed
     */
    public function addUser($data)
    {
        $data['addtime'] = $this->addtime;
        $data['rand_string'] = $this->rand_string;

        $ret = $this->db->insert($this->table,$data);
        if($ret){
            return $id =$this->db->Insert_ID();
        }
    }

    /**
     * 生成随机数
     */
    public function randNum(){
        $this->rand_string = rand(10000,99999);
    }

    /**
     * 修改用户信息
     */
    public function editInfo($uid, $data,$nickname){
        $inviter_id = intval($_SESSION['inviter_id']);
        echo $sql = "UPDATE {$this->table} SET content = '{$data}',inviter_id =$inviter_id,nickname='{$nickname}' WHERE id = {$uid}";
        $result =  $this->db->query($sql);
        return $result ? true : false;
    }
    /**
     * 获取随机数字
     * @return string
     */
    public function getRandNum(){
        return $this->rand_string;
    }

    /**
     * 获取好友
     * @param $aid  活动ID
     * @param $uid  用户ID
     */
    public function getInviter($aid,$uid){
        $where = "inviter_id = $uid and active_id = $aid";
        $sql = $this->query()->select("id,nickname,content")->from($this->table)->where($where)->limit(6)->build();
        return $ret = $this->db->executeS($sql);
    }
}