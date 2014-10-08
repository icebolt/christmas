<?php

/**
 * filname      Third.php
 * author       jinxin
 * Description  Description of Third
 * Date         2014-9-27 15:32:33
 */

/**
 * Description of Third
 *
 * @author jinxin
 */
class ThirdController extends BaseController {
	public $model;
	public function init() {
		parent::init();
		$this->model = new snModel();
	}
	
	public function snAction(){
		$token = $this->getParam('token');
		if(empty($token))
			return $this->displayError('no token');
		$tokenStr = base64_decode($token);
		list($snId,$key) = explode('_', $tokenStr);
		if(empty($snId))
			return $this->displayError('wrong id');
		$snRow = $this->model->getThirdSnById($snId);
		if(empty($snRow) || $key != md5($snRow['id'].$snRow['sn']))
			return $this->displayError('wrong token');
		if($snRow['used'] == 1)
			return $this->displayError("{$snRow['sn']} has been used");
		$this->model->updateThirdSnStatus($snId,1);
		$this->display("{$snRow['type']}/ibb_9_gewala.twig",array('sn'=>$snRow['sn']));
	}
}
//http://activity.bb.bbwc.cn/third/sn/token/MV8yMTY0YWQ0ZWI5NjZlNjczODcxNTcxZTcwNGFmZmRiMA==