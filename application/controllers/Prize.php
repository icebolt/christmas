<?php

/**
 * filname      Prize.php
 * author       fanzhanao
 * Description  Description of Prize
 * Date         2015-06-02 09:37:49
 */

/**
 * Description of Prize
 *
 * @author fanzhanao
 */
class PrizeController extends BaseController {
    private $prizeModel;
    private $winPirzeModel;
    private $pirzeLogModel;
    private $deviceid;
    private $uid;
    private $appid;

    public function init()
    {
        parent::init();
        $this->prizeModel = new prizeModel();
        $this->winPirzeModel = new winPrizeModel();
        $this->pirzeLogModel = new prizeLogModel();
        $this->deviceid = $_SERVER['HTTP_X_SLATE_DEVICEID'];
        $this->uid = $_SERVER['HTTP_X_SLATE_USERID'];
        $this->appid = $_SERVER['X-Slate-AppId'];

        header('Content-type: application/json');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        
        if (!$this->deviceid){
            echo json_encode(array('error'=>'deny access','errno'=>101,'data'=>''));
            exit();
        }
    }
    /** * @return int 抽奖算法
     */
    private function winPrize(){
        $prizes = $this->prizeModel->getAll();
        $prob = array();
	$probabilitySum = 0;
        foreach ($prizes as $prize){
            $prob[$prize['id']] = $prize['probability'];
	    $probabilitySum+=$prize['probability'];
        }
        /**
         * 奖品总几率
         */
	
        $index = 0;
        $rand = mt_rand(1, 10000);
        if ($rand < $probabilitySum){
            $index = $this->randomPrize($prob);
        }
        return $index;
    }

    /**
     * @desc 随即获取奖品
     * @param $prizes array
     * @return int
     */
    private function randomPrize($prizes){
        $index = null;
        $probabilitySum = array_sum($prizes);
        $rand = mt_rand(1, $probabilitySum);
        foreach ($prizes as $key => $value) {
            if ($rand <= $value) {
                $index = $key;
                break;
            } else {
                $rand -= $value;
            }
        }
        return $index;
    }

    /**
     * 检查是否能抽奖
     */
    public function checkAction(){
        $this->pirzeLogModel->deviceid = $this->deviceid ;
        $this->pirzeLogModel->uid = $this->uid;
        $win = $this->pirzeLogModel->checkRaffle();
        if ($win['num']  > 0  ){ //已经抽中过奖品
            $result = array('error'=>'','errno'=>0,'data'=>0);
        }
        else{
            $result = array('error'=>'','errno'=>0,'data'=>1);
        }
        echo json_encode($result);
        exit();
    }

    /**
     * 抽奖
     */
    public function winAction(){
        $id = $this->winPrize();
        $log = new prizeLogModel();
        $log->aid = 0;
        $log->deviceid = $this->deviceid;
        $log->uid = $this->uid;
        if ($id > 0){
            $winPirzeM = new winPrizeModel();
            $winPirzeM->pid = $id;
            $winPirzeM->deviceid = $this->deviceid;
            $winPirzeM->uid = $this->uid;
            $log->pid = $id;
        }
        $log->add();
        
        $result = array('error'=>'','errno'=>0,'data'=>'');
        if ($id > 0){
            $this->prizeModel->id = $id;
	    $prize = $this->prizeModel->get();
            $result['data'] = array('id'=>$prize['id'],'name'=>$prize['name']); 
        }
        echo json_encode($result);
        exit();

    }

}
