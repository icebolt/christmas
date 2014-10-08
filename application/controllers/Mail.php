<?php

/**
 * filname      Mail.php
 * author       jinxin
 * Description  Description of Mail
 * Date         2014-9-26 17:28:30
 */

/**
 * Description of Mail
 *
 * @author jinxin
 */
class MailController extends BaseController {
	public $model;
	public function init() {
		parent::init();
		$this->model = new snModel();
	}
	public function sendAction(){
		$postdata = $this->getparams();
		if(empty($postdata['to'])){
			echo json_encode($this->error($postdata));return;
		}
		if(empty($postdata['from'])){
			echo json_encode($this->error($postdata));return;
		}
		if(empty($postdata['toName'])){
			echo json_encode($this->error($postdata));return;
		}
		if(empty($postdata['content'])){
			echo json_encode($this->error($postdata));return;
		}
		$sn = $this->model->getSn(5184000);
		$third = $this->model->getThirdSn('GeWaLa');
		$renderData = array(
			'to'=>$postdata['toName'],
			'from'=>$postdata['from'],
			'thumb'=>$postdata['thumb'],
			'sn'=>$sn['num'],
			'contents'=>$postdata['content']);
		if(!empty($third))
			$renderData['token'] = base64_encode($third['id'].'_'.md5($third['id'].$third['sn']));
		$html = $this->render('Mail/ibb_9_share.twig',$renderData);
		$mailData = array(
			'to'=>$postdata['to'],
			'from'=>'noreply@bbwc.cn',
			'fromname'=>$postdata['from'],
			'subject'=>"送你一张商周电子卡片！",
			'contents'=>$html
		);
		$ret = $this->getHttpResponse($this->mailApi.'/api/customSend',$mailData);
//		echo $this->mailApi.'/api/customSend';
		if($ret['status'] == 'success' && $ret['data']['message'] == 'success'){
			$this->model->updateSnStatus($sn['id'],3);
			if($third)
				$this->model->updateThirdSnStatus($third['id'],2);
			$this->model->addLog(array('userid'=>uniqid(),'sn'=>$sn['num'],'time'=>$_SERVER['REQUEST_TIME']));
		}
		echo json_encode($ret);
		return;
	}
	public function testTplAction(){
//		$this->display('Mail/ibb_9_share.twig');
		$sn = $this->model->getSn(5184000);
		$third = $this->model->getThirdSn('GeWaLa');
		echo $third['id'].'_'.md5($third['id'].$third['sn']);
		$this->model->addLog(array('userid'=>uniqid(),'sn'=>$sn['num'],'time'=>$_SERVER['REQUEST_TIME']));
		echo $this->render('Mail/ibb_9_share.twig',array(
			'to'=>'sdfsfs',
			'from'=>'jinxin',
			'thumb'=>'http://debug.bbwc.cn/jinxin/yaf_active/public/html/iBBSepShare/images/src/friend/1.png',
			'token'=> base64_encode($third['id'].'_'.md5($third['id'].$third['sn'])),
			'sn'=>$sn['num'],
			'contents'=>'六十多家发上来稍等发了开始'));
	}
}
