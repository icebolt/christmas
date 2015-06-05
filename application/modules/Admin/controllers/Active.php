<?php

/**
 * filname      Active.php
 * author       jinxin
 * Description  Description of Active
 * Date         2015-6-5 18:09:14
 */

/**
 * Description of Active
 *
 * @author jinxin
 */
class ActiveController extends BaseController {
	public function init() {
		parent::init();
	}
	
	public function manageAction(){
		$this->display('active/index.twig');
	}
	
	public function listAction(){
		
	}
	
	public function logsAction(){
		$id = $this->getParam('id',0);
		$appid = $this->getParam('appid',0);
		
	}
}
