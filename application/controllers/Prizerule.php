<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/22
 * Time: 下午3:37
 */
class PrizeruleController extends BaseController
{
    //活动ID
    private $active_id = 0;
    private $startTime = "2015-12-13";
    private $endTime = "2016-02-20";
    /**
     * @var array 间隔时间段
     */
    private $interval = array();
    /**
     * @var array  当天的所处的时间段
     */
    private $position = array();
    public function indexAction()
    {
        echo 123;
    }

    /**
     * 用户抽奖
     */
    public function winAction()
    {
        //开始抽奖
        $prize = $this->_rule();
        $this->returnJson(200, $prize);
    }

    public function init()
    {
        parent::init();
        $this->uid = 2;
        $this->active_id = 2;
    }

    /**
     * 抽奖规则
     * 1.生成随机数 1-100000(10W) 因为数据库是保留5位小数所以乘以10W可以得到整数
     * 2.根据活动ID去奖品等级表（prize_level）查询奖品等级
     * 3.先查询是否有特殊大奖level =99 （中奖概率为100%） 有的话，执行下是否有中奖奖品 ，有中奖奖品直接中奖，返回中奖信息
     * 4.删掉数值中的level=99的特殊大奖
     * 执行循环中奖操作，中奖返回中奖信息，不中奖返回不中奖信息
     * 例如：
     *      1等奖中奖率0.001
     *      2等奖中奖率0.005
     *      3等奖中奖率0.01
     *      4等奖中奖率0.02
     *      生成的随机数为300
     *      100000*0.001 = 100；
     *      100 < 300  没中1等奖
     *      100000*（0.001+0.005）= 600
     *      600 >  300 恭喜中得2等奖
     * 去2等奖中的奖品中取出奖品（如果已经抽完，返回没有中奖）
     * 随机抽取一个2等奖奖品返回用户
     *
     */
    private function _rule(){

        //随机数
        $rand_num = rand(1,100000);
        //获取活动ID奖品等级信息
        $levelInfo = $this->_getLevelInfo(1);
        //是否有99特殊大奖
        if($levelInfo){
            //查询奖品列表
            $prizeInfo = $this->_getPrizeList($levelInfo[0]['id']);
            if($prizeInfo){
                $count = count($prizeInfo);
                $num = rand(0,$count-1);
                //中奖的话直接终止代码返回；
                $arr = $prizeInfo[$num];
                $this->returnJson(200, $arr);
            }
        }
        //其他奖项
        $levelInfo = $this->_getLevelInfo();
        //是否存在其他奖项
        if($levelInfo){
            //判断是否中奖
            $level = $this->_isWin($levelInfo, $rand_num);
            if($level){
                //取出奖品等级奖品
                $prizeInfo = $this->_getPrizeList($level['id']);
                if($prizeInfo){
                    $count = count($prizeInfo);
                    $num = rand(0,$count-1);
                    //中奖的话直接终止代码返回；
                    $arr = $prizeInfo[$num];
                    $this->returnJson(200, $arr);
                }
            }
        }

        //下面属于没中奖信息
        $arr = [
            'id' => 0,
            'name'=> '没有中奖'
        ];
        $this->returnJson(200, $arr);



    }

    public function TestAction()
    {
        $this->initInterval(2);
        echo "interval=>";
        var_dump($this->interval);
        echo "position=>";
        var_dump($this->position);
    }
    /**
     * @return array 获取可供中奖的奖品
     */
    private function prizeAvalible(){
        $prizes = $this->prizeModel->getAll();
        $filterPrize = array();
        if (count($prizes) > 0){
            foreach ($prizes as $key => $prize){
                if (intval($prize['frequency']) > 1){
                    $position = $this->position[$prize['frequency']];
                    $start_time = $this->interval[$prize['frequency']][$position];
                    $end_time = $start_time+86400*$prize['frequency']-1;
                    $this->winPirzeModel->pid = $prize['id'];
                    $num = $this->winPirzeModel->fetchWinNum($start_time,$end_time);
                    if ($num && intval($num['num']) >= intval($prize['num'])){//已经抽过了奖品
                        continue;
                    }else{
                        $filterPrize[] = $prize;
                    }
                }else{
                    $filterPrize[] = $prize;
                }
            }
        }
        return $filterPrize;
    }
    private function initInterval($step){
        $endtime = strtotime($this->endTime);
        $start = $inteval = strtotime($this->startTime);
        $i = 0;

        while ($inteval < ($endtime- $step*86400)){
            $this->interval[$step][] = date('Y-m-d',$inteval = $start + $i*86400);
            $i+=$step;
            var_dump($i);
        }
        echo "step:";
        var_dump($step);
        /**
         * 当天时间
         */
        $currentTime = date('Y-m-d');

        foreach ($this->interval[$step] as $key => $value){
            if ($currentTime < $value){
                $this->position[$step] = $key-1;
                break;
            }
        }
    }

    private function _isWin($levelInfo, $rand_num){
        $num = 0;
        $winInfo = [];
        foreach($levelInfo as $k => $v){
            $num += $v['probability'];
            if($num > $rand_num){
                $winInfo = $levelInfo[$k];
                break;
            }
        }
        return $winInfo;
    }
    /**
     * 获取奖品列表
     * @param $level_id
     * @return mixed
     */
    private function _getPrizeList($level_id){
        $prizeModel = new prizeModel();
        $prizeList = $prizeModel->getList($this->active_id, $level_id);
        return $prizeList;
    }
    /**
     * 获取奖品等级信息
     * @param int $type 0 =>抽奖等级 1 =>秒杀奖等级
     */
    private function _getLevelInfo($type = 0){
        $prizeLevelModel = new prizeLevelModel();
        $prizes = $prizeLevelModel->getList($this->active_id, $type);
        $filterPrize = array();
        if (count($prizes) > 0){
            foreach ($prizes as $key => $prize){
                if (intval($prize['frequency']) > 0){
                    $position = $this->position[$prize['frequency']];
                    $start_time = $this->interval[$prize['frequency']][$position];
                    $end_time = $start_time+86400*$prize['frequency']-1;
                    $this->winPirzeModel->pid = $prize['id'];
                    echo "startTime:".date('Y-m-d');
                    echo "endTime:".date("Y-m-d");
                    $num = $this->winPirzeModel->fetchWinNum($start_time,$end_time);
                    if ($num && intval($num['num']) >= intval($prize['num'])){//已经抽过了奖品
                        continue;
                    }else{
                        $filterPrize[] = $prize;
                    }
                }else{
                    $filterPrize[] = $prize;
                }
            }
        }
        return $filterPrize;
        return $info;
    }


    /**
     * 检查是否能抽奖
     */
    private function check()
    {
        $activeModel = new activeModel();
        $ret = $activeModel->getActive($this->active_id);
        $extra_data=json_decode($ret[0]["extra"],true);
        if($extra_data["require_addinfo"]!==false){
            //是否完善信息
            $activeUserModel = new activeUserModel();
            $info = $activeUserModel->getUserInfo($this->uid);
            //var_dump($info);
            if(empty($info['content'])){
                $this->returnJson(202);
            }
        }

        //是否抽过奖
        $winprizelogModel = new winprizelogModel();
        $num = $winprizelogModel->checkIsWin($this->active_id, $this->uid);
        if ($num['num'] == 0) {
            return 1;
        } elseif ($num['num'] == 1) {
            //查询好友是否满足6人
            $activeUserModel = new activeUserModel();
            $ret_list = $activeUserModel->getInviter($this->active_id, $this->uid);
            if (count($ret_list) == 6) {
                return 2;
            }
        }elseif ($num['num'] == 2) {
            return 3;
        }
        return false;
    }

}