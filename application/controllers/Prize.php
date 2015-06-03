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
    private $startTime = '2015-06-08';
    /**
     * @var 活动结束时间
     */
    private $endTime = '2015-06-21';
    /**
     * @var array 间隔时间段
     */
    private $interval = array();
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

        $this->prizeModel = new prizeModel();
        $this->winPirzeModel = new winPrizeModel();
        $this->pirzeLogModel = new prizeLogModel();

        $this->interval = array(
            '2'=> array(),
            '3' => array()
        );

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
        $id = $this->winPrize();
        $log = new prizeLogModel();
        $log->aid = 0;
        $log->deviceid = $this->deviceid;
        $log->uid = $this->uid;
        if ($id > 0) {
            $winPirzeM = new winPrizeModel();
            $winPirzeM->pid = $id;
            $winPirzeM->deviceid = $this->deviceid;
            $winPirzeM->uid = $this->uid;
            $winPirzeM->add();

            $log->pid = $id;
        }
        $log->add();

        $result = array('error' => '', 'errno' => 0, 'data' => '');
        if ($id > 0) {
            $this->prizeModel->id = $id;
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
            $winPrize = new winPrizeModel();
            $winPrize->contact = json_encode($param);
            $winPrize->pid = $id;
            $winPrize->deviceid = $this->deviceid;
            $winPrize->received = 1;
            $winPrize->uid = $this->uid;
            $winPrize->save();
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
        $prizes = $this->prizeModel->getAll();
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

    private function checkPrizeAvalible(){

    }

}
