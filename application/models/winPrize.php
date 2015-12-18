<?php
/**
 * Created by PhpStorm.
 * User: fanzhanao
 * Date: 15/6/1
 * Time: 下午6:42
 */

class winPrizeModel extends baseModel {


    /**
     * @var $table
     */
    private $table = 'winprize';
    /**
     * @var $pid 奖品ID
     */
    public  $pid;
    /**
     * @var $deviceid 设备ID
     */
    public $deviceid;
    /**
     * @var $uid 用户UID
     */
    public $uid;
    /**
     * @var $addtime 中奖时间
     */
    public $addtime;

    /**
     * @var $received 是否领奖
     */
    public $received;

    /**
     * @var $status 是否发送奖品
     */
    public $status;

    /**
     * @var $contact 联系方式
     */

    public $contact;

    public function __construct(){
        parent::__construct();

        $this->pid = 0;
        $this->uid = 0;
        $this->addtime = time();
        $this->received = false;
        $this->status = 0;
    }


    /**
     * 查询制定时间的中奖数
     */
    public function fetchWinNum($starttime,$endtime){
        $sql = $this->query()->select('count(*) AS num')->from($this->table)->where("pid={$this->pid} AND addtime >= '{$starttime}'  AND addtime <= '{$endtime}'")->build();
        return $this->db->getRow($sql);
    }

    public function checkUserPrize($daily = true){
        $where = "deviceid='{$this->deviceid}' ";
        if ($daily != false){
            $starttime = strtotime(date('Ymd'));
            $endtime = strtotime(date('Ymd 23:59:39'));
            $where.=" AND addtime >= {$starttime} AND addtime <= {$endtime} AND received=0 ";
        }
        $sql = $this->query()->select('*')->from($this->table)->where($where)->build();
        return $this->db->getRow($sql);
    }
    public function add(){
        $data = array(
            'pid'=>intval($this->pid),
            'deviceid' => $this->deviceid,
            'uid' => intval($this->uid),
            'addtime' => $this->addtime,
            'received' => 0,
            'status' => 0,
            'contact' => $this->contact
        );
        return $this->db->insert($this->table,$data);
    }

    public function save(){
        $update = array('contact'=>$this->db->escape($this->contact),'received'=>$this->received);
        $where = "pid={$this->pid} AND deviceid='{$this->deviceid}'";
        $this->db->update($this->table,$update,$where);
    }

    public function getList(){
        $sql = "SELECT a.pid,a.active_id,a.active_uid as auid,b.name as prizeName,c.nickname as nickname FROM `winprize` a  LEFT JOIN  `prize` b ON a.pid = b.id LEFT JOIN `active_user` c ON a.active_uid = c.id ORDER BY a.addtime DESC LIMIT 3
";
//        $sql = $this->query()->select('*')->from($this->table)->where(' contact !=\'\'')->build();
        return $this->db->executeS($sql);
    }
    /*
    public function add(){
        
    }

    public function update(){

    }

    public function fetchRow(){

    }

    public function delete(){

    }
    */
    /**
     * 添加中奖人信息
     * @param $data
     * @return mixed
     */
    public function addWin($data){
        $data['addtime'] = $this->addtime;
        return $this->db->insert($this->table,$data);
    }

    /**
     * 查看是否已经中奖
     * @param $active_id
     * @param $uid
     * @return mixed
     */
    public function checkIsWin($active_id, $uid)
    {
        $where = "active_uid =$uid and active_id = $active_id";
        $sql = $this->query()->select("count(*) as num")->from($this->table)->where($where)->build();
        return $ret = $this->db->getRow($sql);
    }
}
