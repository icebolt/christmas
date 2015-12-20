<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/10
 * Time: 上午11:28
 */
class ActiveController extends BaseController
{
    private $uid = 0;
    private $active_id = 0;

    /**
     * 初始化方法
     */
    public function init()
    {

        $token = htmlspecialchars($_POST['token']);
        $uid = intval($_POST['uid']);
        $active_id = intval($_POST['active_id']);
        if(!isset($active_id) || !isset($token) || !isset($uid) || !$token || !$uid || !$active_id){
            $this->returnJson(100);
        }
        //判断用户是否存在
        if(!$this->CheckUser($uid, $token)){
            $this->returnJson(101);
        }
        //判断活动是否在有效期
        $this->_activeValid($active_id);
        $this->uid = $uid;
        $this->active_id = $active_id;
    }

    /**
     * 判断是否用户合法
     * @param $uid
     * @param $token
     * @return bool
     */
    private function CheckUser($uid, $token)
    {
        $userModel = new activeUserModel();
        $userInfo = $userModel->getUserInfo($uid);
        $local_token = md5($userInfo['id'].'@'. $userInfo['open_id'].'@'.$userInfo['type'].'@'.$userInfo['rand_string']);
        if($local_token != $token){
            return false;
        }
        return true;
    }

    private function _activeValid($active_id)
    {
        $activeModel = new activeModel();
        $ret = $activeModel->getActive($active_id);
        if(!$ret){
            //没有活动
            $this->returnJson(104);
        }
        return true;
    }

    /**
     * 抽奖活动列表
     */
    public function IndexAction()
    {
        echo 123;
    }
    public function addInfoAction()
    {
        $phone = htmlspecialchars($_POST['phone']);
        $weixin = htmlspecialchars($_POST['weixin']);
        $name = htmlspecialchars($_POST['name']);
        $address = htmlspecialchars($_POST['address']);
        $data = [
            'phone' => $phone,
            'weixin' => $weixin,
            'name' => $name,
            'address' => $address
        ];
        $activeUserModel = new activeUserModel();
	$ret = $activeUserModel->editInfo($this->uid,json_encode($data,JSON_UNESCAPED_UNICODE),$name);
	if ($ret) {
            $_SESSION['content'] = json_encode($data);
            $this->returnJson(200);
        }else{
            $this->returnJson(102);
        }
    }

    public function getUserInfoAction(){
        $activeUserModel = new activeUserModel();
        $ret = $activeUserModel->getUserInfo($this->uid);
        if ($ret) {
            $this->returnJson(200,$ret);
        }else{
            $this->returnJson(102);
        }
    }
    /**
     * 是否微信打开
     * @return bool
     */
    function is_weixin(){
        if ( strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) {
            return true;
        }
        return false;
    }

    /**
     * 获取好友列表只获取6个
     */
    public function inviterAction()
    {
        $activeUserModel = new activeUserModel();
        $ret_list = $activeUserModel->getInviter($this->active_id, $this->uid);
        $this->returnJson(200,$ret_list);
    }

    /**
     * 用户抽奖
     */
    public function winAction()
    {
        //判断是否可以抽奖
        if(!$this->check()){
            $this->returnJson(201);
        }
        //开始抽奖
        $prize = $this->_rule();
        $this->returnJson(200, $prize);
    }

    /**
     * 规则
     */
    private function _rule()
    {
//    奖品分为：普通奖品以及特殊奖品
//    普通奖品的抽取规则——随机，用户获得礼品的概率，根据配置的概率表来获得
//    如：用户A获得奖品的概率表
//    ||物品ID||获得物品的概率||
//    普通奖项：每天可以中奖的物品
//      物品ID  剩余数量  中奖概率
//      | 01 || 3 || 1% |
//      | 02 ||10 || 1.5% |
//      | 03 ||12 || 2% |
//      如果角色A抽中的物品，剩余数量为0，则显示用户中四等奖
//
//      特等大奖
//      开放时间：2015/12/25 00:00:00

        $arr = []; //获奖信息
        $prizeModel = new prizeModel();
        $winprizeModel = new winPrizeModel();
        $winprizelogModel = new winprizelogModel();
        $prize = $prizeModel->getActiveGoods($this->active_id, 1);
        if($prize){
            //恭喜你获取特殊大奖
            //更新大奖状态
            $r = $prizeModel->decRemain($prize['id']);
            if($r){
                $arr['id'] = $prize['id'];
                $arr['name'] = $prize['name'];
                $arr['img_url'] = $prize['img_url'];
                //添加到中奖表（因为是100%中奖）

                $data =[
                    'pid'=>$arr['id'],
                    'active_id'=>$this->active_id,
                    'active_uid'=>$this->uid
                ];
                $winprizeModel->addWin($data);
                //添加到中奖日志表

                $data = [
                    'pid' => $arr['id'],
                    'aid' => $this->active_id,
                    'uid' => $this->uid
                ];
                $winprizelogModel->addWin($data);
                return $arr;
            }
        }
        /*
         * 普通奖品中抽奖
         */
        //1 提取当天奖品 1，2，3 奖品
        $prize2 = $this->prizeAvalible();
        $count = count($prize2);
        if($count>1){
            //抽到的数字
            $rand_num = rand(1, 10000);  //33
            //中奖概率
            //     | 01 || 3 || 1% |
            //      | 02 ||10 || 1.5% |
            //      | 03 ||12 || 2% |
            $win_number = 10000 * 0.015; //450
            if ($rand_num <= $win_number) {
                //我中奖了


                $id = rand(0, $count - 1); //中奖ID
                $r = $prizeModel->decRemain($prize2[$id]['id']);
                if ($r) {
                    $arr['id'] = $prize2[$id]['id'];
                    $arr['name'] = $prize2[$id]['name'];
                    $arr['img_url'] = $prize2[$id]['img_url'];
                    $data = [
                        'pid' => $arr['id'],
                        'active_id' => $this->active_id,
                        'active_uid' => $this->uid
                    ];
                    $winprizeModel->addWin($data);
                    //添加到中奖日志表
                    $data = [
                        'pid' => $arr['id'],
                        'aid' => $this->active_id,
                        'uid' => $this->uid
                    ];
                    $winprizelogModel->addWin($data);
                    return $arr;
                }

            }
        }
        $arr = ['id'=> 0 , 'name'=>'iWeekly贺卡一张'];
        //添加到中奖日志表
        $winprizelogModel = new winprizelogModel();
        $data = [
            'pid' => 0,
            'aid' => $this->active_id,
            'uid' => $this->uid
        ];
        $winprizelogModel->addWin($data);
        return $arr;
    }

    /**
     * @return array 获取可供中奖的奖品
     */
    private function prizeAvalible(){
        $start_time = "2015-12-21 00:00:00";
        $end_time = "2015-12-31 23:59:59";
        $prizeModel = new prizeModel();
        $winprizeModel = new winPrizeModel();
        $prizes = $prizeModel->getActiveGoods($this->active_id);
        $filterPrize = array();
        if (count($prizes) > 0){
            foreach ($prizes as $key => $prize){
                $winprizeModel->pid = $prize['id'];
                $num = $winprizeModel->fetchWinNum($start_time,$end_time);
                if ($num && intval($num['num']) >= intval($prize['num'])){//已经抽过了奖品
                    continue;
                }else{
                    $filterPrize[] = $prize;
                }
            }
        }
        return $filterPrize;
    }

    public function checkUserAction(){
        $ret = $this->check();
        if($ret){
            $this->returnJson(200);
        }else{
            $this->returnJson(201);
        }
    }
    /**
     * 检查是否能抽奖
     */
    private function check()
    {
        //是否完善信息
        $activeUserModel = new activeUserModel();
        $info = $activeUserModel->getUserInfo($this->uid);
        //var_dump($info);
	if(empty($info['content'])){
            $this->returnJson(202);
        }
        //是否抽过奖
        $winprizelogModel = new winprizelogModel();
        $num = $winprizelogModel->checkIsWin($this->active_id, $this->uid);
        if ($num['num'] == 0) {
            return true;
        } elseif ($num['num'] == 1) {
            //查询好友是否满足6人
            $activeUserModel = new activeUserModel();
            $ret_list = $activeUserModel->getInviter($this->active_id, $this->uid);
            if (count($ret_list) == 6) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取最新获奖
     */
    public function WinlistAction()
    {
        $winprizeModel = new winPrizeModel();
        $winprize = $winprizeModel->getList();
        $count = count($winprize);
        $num = rand(0,$count-1);
        $activeUserModel = new activeUserModel();
        $nickname = $activeUserModel->getUserInfo($winprize[$num]['active_uid']);
        $prizeModel = new prizeModel();
        $prizeModel->id = $winprize[$num]['pid'];
        $prizeName = $prizeModel->get();
        $arr = [
            'nickname' => $nickname['nickname'],
            'prizeName'=> $prizeName['name']
        ];

        $this->returnJson(200, $arr);
    }
}