<?php
/**
 * Created by PhpStorm.
 * User: fanzhanao
 * Date: 15/6/1
 * Time: 下午6:40
 */

class prizeModel extends baseModel{

    private $table = 'prize';
    /**
     * @var $id  奖品ID
     */
    public $id;

    /**
     * @var $aid 活动ID
     */
    public $aid;

    /**
     * @var $name 活动名称
     */
    public $name;

    /**
     * @var $frequency 中奖几率
     */
    public $frequency;

    /**
     * @var $num 单频次中奖数量
     */
    public $num;

    /**
     * @var 奖品总数
     */
    public $total;

    /**
     * @var 剩余数量
     */
    public $remain;
    /**
     * @var $probability  中奖几率
     */
    public $probability;


    public function __construct(){
        parent::__construct();
    }

    public function getAll(){
        $where = 'remain >0';
        if ($this->aid){
            $where.=' AND aid='.$this->aid;
        }
        $sql = $this->query()->select('*')->from($this->table)->where($where)->build();
        $row = $this->db->executeS($sql);
        return $row;
    }
    /**
     * 获取单条数据
     */
    public function get(){
        $sql = $this->query()->select('*')->from($this->table)->where("id={$this->id}")->build();
        $row = $this->db->getRow($sql);
	    return $row;
    }

    public function decreaseRemain(){
        $sql = "UPDATE {$this->table} SET remain = remain -1 WHERE id = {$this->id} AND remain > 0 ";
        $result =  $this->db->query($sql);
        return $result ? true : false;
    }

    /**
     * ========================================微信活动方法========================================
     */
    /**
     * @param $active_id
     * @param int $frequency 为0表示不限制
     */
    public function getActiveGoods($active_id, $frequency = 0){
        $start_time = date('Y-m-d H:i:s',time());
        if($frequency == 0){
            $where = "once = 0 and aid ={$active_id} and remain > 0";
            $sql = $this->query()->select('*')->from($this->table)->where($where)->build();
            $row = $this->db->executeS($sql);
            return $row;
        }else{
            $where = "once = 1 and aid ={$active_id} and remain > 0 and start_time <'{$start_time}'";
            $sql = $this->query()->select('*')->from($this->table)->where($where)->build();
            $row = $this->db->getRow($sql);
            return $row;
        }
    }
    public function decRemain($prize_id){
        $sql = "UPDATE {$this->table} SET remain = remain -1 WHERE id = {$prize_id} AND remain > 0 ";
        $result =  $this->db->query($sql);
        if($result){
            $res = $this->db->Affected_Rows();
        }
        return $res ? true : false;
    }
    /**
     * 获取奖品列表
     */
    public function getList($active_id, $level_id, $type = 0)
    {
        $time = date('Y-m-d H:i:s',time());

        $where = "level_id = $level_id and aid ={$active_id} and remain > 0 ";
        if($type == 1){
            $where .=" and start_time <'{$time}' and end_time > '{$time}'";
        }
        echo $sql = $this->query()->select('*')->from($this->table)->where($where)->build();
        $row = $this->db->executeS($sql);
        return $row;
    }

}
