<?php
/**
 * Created by PhpStorm.
 * User: fanzhanao
 * Date: 15/6/1
 * Time: 下午6:43
 */

class prizeLogModel extends baseModel {

    /**
     * @var $table
     */
    private $table = 'prizelog';

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
    public $uptime;

    /**
     * @var $received 是否领奖
     */
    public $aid;

    /**
     * @var $status 是否发送奖品
     */
    public $pid;

    /**
     * @var $contact 联系方式
     */

    public $contact;

    public function __construct(){
        parent::__construct();

        $this->pid = 0;
        $this->uid = 0;
        $this->uptime = time();
    }

    public function add(){

    }


}