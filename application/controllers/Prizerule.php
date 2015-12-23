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
    private $token = "";
//    private $endTime = "0";
    /**
     * 活动信息
     * @var array
     */
    private $activeInfo = [];
    private $userInfo = [];
    /**
     * @var array 间隔时间段
     */
    private $interval = array();
    /**
     * @var array  当天的所处的时间段
     */
    private $position = array();

    public function init()
    {
        parent::init();
        $uid = I('uid',2,'intval');
        $active_id = I('active_id',2, 'intval');
        $token = I('token');
        if(!$uid || !$active_id || !$token){
            $this->returnJson(100);
        }
        $this->uid = $uid;
        $this->active_id = $active_id;
        $this->token = $token;
        //获取活动信息
        $this->getActiveInfo();
        //判断用户是否存在
        $this->CheckUser();

    }

    /**
     * 首页入口
     */
    public function indexAction()
    {
        echo "接口正常！";
    }

    /**
     * 判断是否用户合法
     */
    private function CheckUser()
    {
        $userModel = new activeUserModel();
        $userInfo = $userModel->getUserInfo($this->uid);
        $local_token = md5($userInfo['id'].'@'. $userInfo['open_id'].'@'.$userInfo['type'].'@'.$userInfo['rand_string']);
        if($local_token != $this->token){
            $this->returnJson(101);
        }
        $this->userInfo = $userInfo;
    }

    /**
     * 获取活动信息
     */
    private function getActiveInfo()
    {
        $activeModel = new activeModel();
        $activeInfo = $activeModel->getActive($this->active_id);
        if($activeInfo){
            $this->activeInfo = $activeInfo;
        }else{
            $this->returnJson(104);
        }
    }
    /**
     * 用户抽奖
     */
    public function winAction()
    {
        //检查用户资料是否完善
        $this->_checkInfo();
        //检查用户抽奖次数
        $num = $this->_checkWinNum();
        if($num > 0){
           //大于0次的去判断其他规则
           //好友大于等于6可以抽奖
            if($num == 1){
                $this->_inviterNum();
            }else{
                $this->returnJson(205);
            }
        }
        //开始抽奖
        $prize = $this->_rule();
        $this->returnJson(200, $prize);
    }

    /**
     * 计算好友数量
     */
    private function _inviterNum()
    {
        $activeUserModel = new activeUserModel();
        $num = $activeUserModel->getInviter($this->active_id, $this->uid);
        if($num['num'] < 6){
            $this->returnJson(204);
        }
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

    /**
     * 测试方法
     */
    public function TestAction()
    {
        $this->initInterval(2);
        echo "interval=>";
        var_dump($this->interval);
        echo "position=>";
        var_dump($this->position);
    }

    /**
     * 抽奖频次计算
     * @param $step
     */
    private function initInterval($step){
        $endtime = strtotime($this->activeInfo['starttime']);
        $start = $inteval = strtotime($this->activeInfo['endtime']);
        $i = 0;
        while ($inteval < ($endtime- $step*86400)){
            $this->interval[$step][] = date('Y-m-d',$inteval = $start + $i*86400);
            $i+=$step;
        }
        /**
         * 当天时间
         */
        $currentTime = date('Y-m-d H:i:s');

        foreach ($this->interval[$step] as $key => $value){
            if ($currentTime < $value){
                $this->position[$step] = $key-1;
                break;
            }
        }
    }

    /**
     * 是否抽中奖
     * @param $levelInfo 奖池
     * @param $rand_num  抽奖号码
     * @return array
     */
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
     * @param int $type  0 =>抽奖等级 1 =>秒杀奖等级
     * @return array
     */
    private function _getLevelInfo($type = 0){
        $prizeLevelModel = new prizeLevelModel();
        $winprizeModel = new winPrizeModel();
        $prizes = $prizeLevelModel->getList($this->active_id, $type);
        $filterPrize = array();
        if (count($prizes) > 0){
            foreach ($prizes as $key => $prize){
                if (intval($prize['frequency']) > 0){
                    $this->initInterval($prize['frequency']);
                    $position = $this->position[$prize['frequency']];
                    $start_time = $this->interval[$prize['frequency']][$position];
                    $end_time = $start_time+86400*$prize['frequency']-1;
                    $num = $winprizeModel->fetchWinNum($this->active_id, $prize['id'], $start_time,$end_time);
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

    /**
     * 检查是否完善信息
     */
    private function _checkInfo()
    {
        $ret = $this->activeInfo;
        $extra_data=json_decode($ret[0]["extra"],true);
        if($extra_data["require_addinfo"]!==false){
            //是否完善信息
            $info = $this->userInfo;
            if(empty($info['content'])){
                $this->returnJson(202);
            }
        }
    }

    /**
     * 查询抽奖次数
     * @return mixed
     */
    private function _checkWinNum()
    {
        //是否抽过奖
        $winprizelogModel = new winprizelogModel();
        $num = $winprizelogModel->checkWinNum($this->active_id, $this->uid);
        return $num;
    }

}