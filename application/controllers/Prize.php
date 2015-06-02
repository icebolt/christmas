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
class PrizeController extends \SlatePF\Extras\ExtrasController {
    private $prizeModel;
    private $winPirzeModel;
    private $pirzeLogModel;

    public function __construct() {
        $this->prizeModel = new prizeModel();
        $this->winPirzeModel = new winPrizeModel();
        $this->pirzeLogModel = new prizeLogModel();
    }

    /**
     * @return int 抽奖算法
     */
    private function winPrize(){
        $prizes = $this->prizeModel->getAll();
        $prob = array();
        foreach ($prizes as $prize){
            $prob[$prize->id] = $prize->probability;
        }
        /**
         * 奖品总几率
         */
        $probabilitySum = array_sum($prob);

        $index = 0;
        $rand = mt_rand(1, 10000);
        if ($rand < $probabilitySum){
            $index = $this->randomPrize($prizes);
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
        foreach ($prizes as $key => $probability) {
            if ($rand <= $probability) {
                $index = $key;
                break;
            } else {
                $rand -= $probability;
            }
        }
        return $index;
    }

    /**
     * 检查是否能抽奖
     */
    public function checkAction(){

        $win = $this->winPirzeModel->checkUser(true);
        if ($win['num']  > 0  ){ //已经抽中过奖品
            $result = array('error'=>'','errno'=>0,'data'=>0);
        }
        else{
            $result = array('error'=>'','errno'=>0,'data'=>0);
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
        $deviceid = '';
        $uid = 0;
        $log->aid = 0;
        $log->deviceid = '';
        $log->uid = $uid;
        if ($id > 0){
            $winPirzeM = new winPrizeModel();
            $winPirzeM->pid = $id;
            $winPirzeM->deviceid = $deviceid;
            $winPirzeM->uid = $uid;
            $log->pid = $id;
        }else{
            $log->add();
        }
        $result = array('error'=>'','errno'=>0,'data'=>'');
        if ($id > 0){
            $this->prizeModel->id = $id;
            $result['data'] = $this->prizeModel->get();
        }

        echo json_encode($result);
        exit();

    }

}
