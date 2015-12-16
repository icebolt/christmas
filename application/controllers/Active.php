<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/10
 * Time: 上午11:28
 */
class ActiveController extends \SlatePF\Extras\ExtrasController
{
    private $uid = 0;
    private $active_id = 0;
    /**
     * 初始化方法
     */
    public function init()
    {

        //验证用户是否合法
//        if(!$this->is_weixin()){
//            echo "请使用微信浏览器打开";
//            exit;
//        }
        $token = htmlspecialchars($_POST['token']);
        $uid = intval($_POST['uid']);
        $active_id = intval($_POST['active_id']);
        if(!isset($active_id) || !isset($token) || !isset($uid) || !$token || !$uid || !$active_id){
            $ret = [
              'error'=> '数据不合法',
                'errno' => 100,
                'data'=>''
            ];
            echo json_encode($ret);
            exit;
        }
        //判断用户是否存在
        if(!$this->CheckUser($uid, $token)){
            $ret = [
                'error'=> '用户不存在',
                'errno' => 101,
                'data'=>''
            ];
            echo json_encode($ret);
            exit;
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
            $ret = [
                'error'=> '活动不存在或者已经结束',
                'errno' => 104,
                'data'=>''
            ];
            echo json_encode($ret);
            exit;
        }else{
            return true;
        }
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
        $ret = $activeUserModel->editInfo($this->uid,json_encode($data));
        if ($ret) {
        $info = [
            'error' => 'success',
            'errno' => 0,
            'data' => ''
        ];
        }else{
            $info = [
                'error' => '失败',
                'errno' => 100,
                'data' => ''
            ];
        }
        echo json_encode($info);
        exit;
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
        $ret = [
            'error'=> '',
            'errno' => 0,
            'data'=> $ret_list
        ];
        echo json_encode($ret);
        exit;
    }

    /**
     * 用户抽奖
     */
    public function winAction()
    {
        //判断是否可以抽奖
        if(!$this->check()){
            $ret = [
                'error'=> '已经抽过奖',
                'errno' => 0,
                'data'=> ''
            ];
            echo json_encode($ret);
            exit;
        }
        //开始抽奖
        $prize = $this->_rule();
        $ret = [
            'error'=> '',
            'errno' => 0,
            'data'=> $prize
        ];
        echo json_encode($ret);
        exit;



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
//     ||1||80%||
//     ||2||10%||
//     ||3||5%||
//     ||4||3%||
//     ||5||1%||
//     ||6||1%||
        $arr = []; //获奖信息
        $prizeModel = new prizeModel();
        $prize = $prizeModel->getActiveGoods($this->active_id, 1);
        if($prize){
            //恭喜你获取特殊大奖
            //更新大奖状态
            $r = $prizeModel->decRemain($prize['id']);
            if($r){
                $arr['id'] = $prize['id'];
                $arr['name'] = $prize['name'];
                //添加到中奖表（因为是100%中奖）
                $winprizeModel = new winPrizeModel();
                $data =[
                    'pid'=>$arr['id'],
                    'active_id'=>$this->active_id,
                    'active_uid'=>$this->uid
                ];
                $winprizeModel->addWin($data);
                //添加到中奖日志表
                $winprizelogModel = new winprizelogModel();
                $data = [
                    'pid' => $arr['id'],
                    'aid' => $this->active_id,
                    'uid' => $this->uid
                ];
                $winprizelogModel->addWin($data);
                return $arr;
            }
        }
        //普通奖品中抽奖
        $prize2 = $prizeModel->getActiveGoods($this->active_id);
        //抽到的数字
        $rand_num = rand(1, 100);  //33
        $num = 0;
        foreach ($prize2 as $key => $val) {

            $num += $val['probability'];
            if ($num >= $rand_num) {
                //恭喜获取这个奖
                $arr['id'] = $val['id'];
                $arr['name'] = $val['name'];
                break;
            }
        }
        //添加到中奖表（因为是100%中奖）
        $winprizeModel = new winPrizeModel();
        $data = [
            'pid' => $arr['id'],
            'active_id' => $this->active_id,
            'active_uid' => $this->uid
        ];
        $winprizeModel->addWin($data);
        //添加到中奖日志表
        $winprizelogModel = new winprizelogModel();
        $data = [
            'pid' => $arr['id'],
            'aid' => $this->active_id,
            'uid' => $this->uid
        ];
        $winprizelogModel->addWin($data);
        return $arr;
//
//     特殊大奖的获奖规则——每日定时发放
//     如：用户B活动大奖的机制
//    ||物品ID||开放奖品的时间||
//     ||1||2015-12-21 23:00||
//     ||2||2015-12-22 23:00||
//     ||3||2015-12-23 23:00||
//     ||4||2015-12-24 23:00||
//     ||5||2015-12-25 23:00||
//     ||6||2015-12-26 23:00||
//    系统在22号晚上11:00准时开放可抽取的大奖“2”，如果此时用户B是在开放大奖后最近一个时间点
//（如2015-12-22 23:01）提交抽奖的用户即可抽取到对应的奖品“2”；
//如果同时有多个用户同时提交表单，则在这几个用户中随机选择一位获得奖品
//        一个用户同时有且仅有可能获得一个奖品，获得大奖的优先级高于普通奖品。
//        即：如果一个用户未抽取到大奖，则进入普通奖品抽取规则；如果用户获得大奖，则同一时间不会获得普通奖品

    }


    /**
     * 检查是否能抽奖
     */
    private function check()
    {
        $winprizeModel = new winprizeModel();
        $num = $winprizeModel->checkIsWin($this->active_id, $this->uid);
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
}