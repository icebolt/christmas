<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/10
 * Time: 上午11:28
 */
class ActiveController extends BaseController
{
    /**
     * 初始化方法
     */
    public function init()
    {
        parent::init();
        //验证用户是否合法
        if(!$this->is_weixin()){
            echo "请使用微信浏览器打开";
            exit;
        }

    }
    /**
     * 抽奖活动列表
     */
    public function IndexAction()
    {
        $model = new activeModel();
        //获取活动信息
        $ret = $model->getActive(1);
        $this->returnJson($ret);
    }
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
        $inviterModel = new inviterModel();
        $ret = $inviterModel->getInviter(1);
        $this->returnJson($ret);
    }

    /**
     * 用户抽奖
     */
    public function winAction()
    {

    }


    /**
     * 检查是否能抽奖
     */
    public function checkAction()
    {
        $win = $this->pirzeLogModel->checkRaffle();

        if ($win['num'] > 0) { //已经抽中过奖品
            $result = array('error' => '', 'errno' => 0, 'data' => 0);
        } else {
            $result = array('error' => '', 'errno' => 0, 'data' => 1);
        }
        //检查是否中奖，但是没有填写资料
        $prize = $this->winPirzeModel->checkUserPrize();
        if ($prize['pid'] > 0) {
            $result = array('error' => '', 'errno' => 0, 'data' => $prize['pid']);
        }
        echo json_encode($result);
        exit();
    }
}