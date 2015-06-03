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

class PrizeController extends BaseController
{
    private $prizeModel;
    private $winPirzeModel;
    private $pirzeLogModel;
    private $deviceid;
    private $uid;
    private $appid;

    /**
     * @var 活动开始时间
     */
    private $startTime = '2015-06-03';
    /**
     * @var 活动结束时间
     */
    private $endTime = '2015-06-21';
    /**
     * @var array 间隔时间段
     */
    private $interval = array();
    /**
     * @var array  当天的所处的时间段
     */
    private $position = array();
    /**
     * 初始化方法
     */
    public function init()
    {
        parent::init();


        header('Content-type: application/json');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Slate-DeviceId,X-Slate-UserId,X-Slate-AppId");

        $this->deviceid = $this->getParam('deviceid'); //$_SESSION['DeviceId'];// $_SERVER['HTTP_X_SLATE_DEVICEID'];
        $this->uid = $this->getParam('uid'); //$_SESSION['Uid'];// $_SERVER['HTTP_X_SLATE_USERID'];

        if (!$this->deviceid) {
            echo json_encode(array('error' => 'deny access', 'errno' => 101, 'data' => ''));
            exit();
        }
//        $this->interval = array(
//            '2'=> array(1433721600,1433894400,1434067200,1434240000,1434412800,1434585600,1434758400),
//            '3' => array(1433721600,1433980800,1434240000,1434499200,1434758400)
//        );

        $this->prizeModel = new prizeModel();
        $this->winPirzeModel = new winPrizeModel();
        $this->pirzeLogModel = new prizeLogModel();

        $this->initInterval(2);
        $this->initInterval(3);
    }

    /**
     * 检查是否能抽奖
     */
    public function checkAction()
    {
        $this->pirzeLogModel->deviceid = $this->winPirzeModel->deviceid = $this->deviceid;
        $this->pirzeLogModel->uid = $this->winPirzeModel->deviceid = $this->uid;
        $win = $this->pirzeLogModel->checkRaffle();

        if ($win['num'] > 0) { //已经抽中过奖品
            $result = array('error' => '', 'errno' => 0, 'data' => 0);
        } else {
            $result = array('error' => '', 'errno' => 0, 'data' => 1);
        }
        //检查是否中奖，但是没有填写资料
        $prize = $this->winPirzeModel->checkUserPrize();
        if ($prize['id'] > 0) {
            $result = array('error' => '', 'errno' => 0, 'data' => $prize['id']);
        }
        echo json_encode($result);
        exit();
    }

    /**
     * 抽奖
     */
    public function winAction()
    {
        //摇奖
        $id = $this->winPrize();
        $log = new prizeLogModel();
        $log->aid = 0;
        $log->deviceid = $this->deviceid;
        $log->uid = $this->uid;
        if ($id > 0) {
            $winPirzeM = new winPrizeModel();
            $this->prizeModel->id = $id;
            $winPirzeM->pid = $id;
            $winPirzeM->deviceid = $this->deviceid;
            $winPirzeM->uid = $this->uid;
            //添加奖品
            $winPirzeM->add();
            /**
             * 减少剩余奖品数量
             */
            $this->prizeModel->decreaseRemain();

            $log->pid = $id;
        }
        $log->add();

        $result = array('error' => '', 'errno' => 0, 'data' => '');
        if ($id > 0) {
            $prize = $this->prizeModel->get();
            $result['data'] = array('id' => $prize['id'], 'name' => $prize['name']);
        }
        echo json_encode($result);
        exit();
    }

    public function contactAction()
    {
        $param = array(
            'name' => $this->getParam('name'),
            'phone' => $this->getParam('phone'),
            'address' => $this->getParam('address')
        );

        $id = intval($this->getParam('pid'));

        if ($id > 0) {
            $this->winPirzeModel->contact = json_encode($param);
            $this->winPirzeModel->pid = $id;
            $this->winPirzeModel->deviceid = $this->deviceid;
            $this->winPirzeModel->received = 1;
            $this->winPirzeModel->uid = $this->uid;
            $this->winPirzeModel->save();
            $result = array('error' => '', 'errno' => 0, 'data' => $id);
        } else {
            $result = array('error' => 'parameter error', 'errno' => 102, 'data' => $id);
        }
        echo json_encode($result);

        exit;
    }

    /**
     * @return int 抽奖算法
     */
    private function winPrize()
    {
        $prizes = $this->checkPrizeAvalible();
        $prob = array();
        $probabilitySum = 0;
        foreach ($prizes as $prize) {
            $prob[$prize['id']] = $prize['probability'];
            $probabilitySum += $prize['probability'];
        }
        /**
         * 奖品总几率
         */

        $index = 0;
        $rand = mt_rand(1, 10000);
        if ($rand < $probabilitySum) {
            $index = $this->randomPrize($prob);
        }
        return $index;
    }

    /**
     * @desc 随即获取奖品
     * @param $prizes array
     * @return int
     */
    private function randomPrize($prizes)
    {
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

    private function initInterval($step){
        $endtime = strtotime($this->endTime)+86400;
        $start = $inteval = strtotime($this->startTime);
        $i = 0;

        while ($inteval < $endtime){
            $this->interval[$step][] = $inteval = $start + $i*3600*24;
            $i+=$step;
        }
        /**
         * 当天时间
         */
        $currentTime = strtotime(date('Y-m-d'));

        foreach ($this->interval[$step] as $key => $value){
            if ($currentTime < $value){
                $this->position[$step] = $key;
                break;
            }
        }
    }

    /**
     * @return array 获取可供中奖的奖品
     */
    private function checkPrizeAvalible(){
        $prizes = $this->prizeModel->getAll();
        if (count($prizes) > 0){
            foreach ($prizes as $key => $prize){
                if ($prize['frequency'] > 1){
                    $position = $this->position[$prize['frequency']];
                    $start_time = $this->interval[$prize['frequency']][$position];
                    $end_time = $start_time+84600*$prize['frequency']-1;
                    $this->winPirzeModel->pid = $prize['id'];
                    $num = $this->winPirzeModel->fetchWinNum($start_time,$end_time);
                    if ($num >= $prize['num']){//已经抽过了奖品
                        unset($prizes[$key]);
                    }
                }
            }
        }
        return $prizes;
    }

}
