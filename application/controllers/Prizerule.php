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
    /**
     * 抽奖随机数
     * @var int
     */
    private $rand_num = 0;

    public function init()
    {
        parent::init();
        $uid = I('uid',2,'intval');
        $active_id = I('active_id',2, 'intval');
        $token = I('token');
        $this->rand_num = rand(1,100000);
        if(!$uid || !$active_id || !$token){
            $this->returnJson(100);
        }
        $this->uid = $uid;
        $this->active_id = $active_id;
        $this->token = $token;
        //获取活动信息
        $this->getActiveInfo();
//        //判断用户是否存在
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
     * 用户抽奖
     */
    public function winAction()
    {
        //检查用户资料是否完善
        $this->_checkInfo();
        //检查用户抽奖次数
//        $num = $this->_checkWinNum();
//        if($num > 0){
//           //大于0次的去判断其他规则
//           //好友大于等于6可以抽奖
//            if($num == 1){
//                $this->_inviterNum();
//            }else{
//                $this->returnJson(205);
//            }
//        }
        //开始抽奖
        file_put_contents('/tmp/newactive.log',"抽奖：".microtime()."\r\n",FILE_APPEND);
        $this->_rule(1);  //特殊大奖
        $this->_rule(0);  //普通抽奖
        $this->_addLog();  //没有中奖写入日志
        $prize = [
            'id' => 0,
            'name'=> '没有中奖'
        ];
        $this->returnJson(200, $prize);
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
    private function _rule($type = 0){
        //随机数
        //获取活动ID奖品等级信息
        $levelInfo = $this->_getLevelInfo($type);   //1 是特殊大奖

        //是否有特殊大奖
        if($levelInfo){
//            echo "levelinfo";
//            var_dump($levelInfo);
            //判断是否中奖
            $level = $this->_isWin($levelInfo);

            if($level){
                //查询奖品列表
                $prizeInfo = $this->_getPrizeList($level['id'], $type);
                if($prizeInfo){
                    //可以
                    $count = count($prizeInfo);
                    $num = rand(0,$count-1);
                    //中奖的话直接终止代码返回；
                    $arr = $prizeInfo[$num];
                    //获奖信息
                    //写入奖品表  写入日志表
                    if($this->_addWin($arr)){
                        file_put_contents('/tmp/newactive.log',"中奖写入成功：". var_export($arr,1)."\r\n",FILE_APPEND);
                        $this->returnJson(200, $arr);
                    }
                    file_put_contents('/tmp/newactive.log',"中奖写入失败：". var_export($arr,1)."\r\n",FILE_APPEND);
                }
            }
        }
    }

    /**
     * 中奖了 写数据库
     * @param $data
     * @return mixed
     */
   private function _addWin($data)
   {

       $prizeModel = new prizeModel();
       $winprizeModel = new winPrizeModel();
       $winprizelogModel = new winprizelogModel();
       //更新大奖状态
       $r = $prizeModel->decRemain($data['id']);
       if($r){
           //添加中奖信息
           $info =[
               'pid'=>$data['id'],
               'active_id'=>$this->active_id,
               'active_uid'=>$this->uid
           ];
           $winprizeModel->addWin($info);
           //添加到中奖日志表
           $info2 = [
               'pid' => $data['id'],
               'aid' => $this->active_id,
               'uid' => $this->uid
           ];
           $winprizelogModel->addWin($info2);
           return true;
       }
       return false;
   }

    /**
     * 没中奖，写数据库
     * @param int $id
     * @return mixed
     */
    private function _addLog($id =0)
    {
        $winprizelogModel = new winprizelogModel();
        $info2 = [
            'pid' => $id,
            'aid' => $this->active_id,
            'uid' => $this->uid
        ];
        $ret = $winprizelogModel->addWin($info2);
        return $ret;
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
    private function _isWin($levelInfo){
        $num = 0;
        $winInfo = [];
        foreach($levelInfo as $k => $v){
            $num += $v['probability']*100000;
            if($num > $this->rand_num){
                $winInfo = $levelInfo[$k];
                break;
            }
        }
        file_put_contents('/tmp/newactive.log',"是否中奖：". var_export($winInfo,1)."\r\n",FILE_APPEND);
        return $winInfo;
    }
    /**
     * 获取奖品列表
     * @param $level_id
     * @return mixed
     */
    private function _getPrizeList($level_id, $type =0){
        $prizeModel = new prizeModel();
        $prizes = $prizeModel->getList($this->active_id, $level_id, $type);
        $winprizeModel = new winPrizeModel();
        $filterPrize = array();
        if (count($prizes) > 0){
            foreach ($prizes as $key => $prize){
                file_put_contents('/tmp/newactive.log','奖品信息:'.var_export($prize,1)."\r\n",FILE_APPEND);
                if (intval($prize['frequency']) > 0){
                    $this->initInterval($prize['frequency']);
                    $position = $this->position[$prize['frequency']];
                    $start_time = $this->interval[$prize['frequency']][$position];
                    $end_time = $start_time+86400*$prize['frequency']-1;
                    $num = $winprizeModel->fetchWinNum($this->active_id, $prize['id'], $start_time,$end_time);
//                    echo "已经抽中的num:";
//                    var_dump($num);
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
//        echo "奖品列表:";
//        var_dump($filterPrize);
        return $filterPrize;
    }
    
    /**
     * 获取奖品等级信息
     * @param int $type  0 =>抽奖等级 1 =>秒杀奖等级
     * @return array
     */
    private function _getLevelInfo($type = 0){
        $prizeLevelModel = new prizeLevelModel();
        $prizes = $prizeLevelModel->getList($this->active_id, $type);
        return $prizes;
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
        return $num['num'];
    }

}