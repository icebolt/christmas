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
     * @var $probability  中奖几率
     */
    public $probability;


    public function __construct(){
        parent::__construct();
    }

    public function getAll(){
        $sql = $this->query()->select('*')->from($this->table)->build();
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

}
