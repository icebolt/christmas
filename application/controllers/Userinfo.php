<?php

/**
 * filname      userInfo.php
 * author       jinxin
 * Description  Description of userInfo
 * Date         2015-5-27 17:13:39
 */

/**
 * Description of userInfo
 *
 * @author jinxin
 */
class UserinfoController extends BaseController {
	public $pre;
	public function init() {
		parent::init();
		$env = ini_get('yaf.environ');
		if($env == 'test'){
			$this->pre = '/yaf_active/public/index.php/';
		}elseif($env == 'product'){
			$this->pre = '/jinxin/yaf_active/public/index.php/';
		}else{
			$this->pre = '/';
		}
	}
	
	public function formAction(){
		$id = $this->getParam('id',0);
		$aid = $this->getParam('aid',0);
		$appid = $this->getParam('appid',0);
		$uid = $this->getParam('uid',0);
		$token = rawurldecode(trim($this->getParam('token','')));
		$api = $this->api['user'].'get&datatype=2';
		$model = new userModel();
		$action = $this->pre.'/userinfo/save';
		
		if(empty($uid) && empty($id)){
			return $this->displayError('no uid');
		}
		
		if(empty($appid) || empty($aid)){
			return $this->displayError('no require params');
		}
		
		if(!empty($uid) && empty($token)){
			return $this->displayError('no token');
		}
		if(!empty($uid) && !empty($token)){
			$post = array('data'=>json_encode(array('uid'=>$uid,'token'=>$token)));
			$res = $this->getHttpResponse($api, $post);
			if(is_array($res['data']) && $res['data']['error']['no'] === 0){
				$data['uid'] = $res['data']['uid'];
				$data['nickname'] = $res['data']['nickname'];
				$data['avatar'] = $res['data']['avatar'];
				$data['token'] = $res['data']['token'];
				$extInfo = $model->get($res['data']['uid']);
				if(!empty($extInfo)){
					$data = array_merge($data,$extInfo);
				}
				$this->assign('user', $data);
				$this->assign('action',$action);
				return $this->display('user/info.twig');
			}
			return $this->display('user/error.twig');
		}elseif(!empty ($id)){
			$user = $model->get($id);
			$this->assign('user', $data['data']);
			$this->assign('action',$action);
			return $this->display('user/info.twig');
		}
		return $this->display('user/error.twig');
	}
	
	public function loginAction(){
		$this->assign('action', $this->pre.'user/dologin');
		$this->display('user/login.twig');
	}
	
	public function dologinAction(){
		$username = $this->getParam('username','');
		$password = $this->getParam('password','');
		$api = $this->api['user'].'login&datatype=2';
		$post = array('data'=>json_encode(array('username'=>$username,'password'=>$password)));
		$res = $this->getHttpResponse($api, $post);
		if(is_array($res['data']) && $res['data']['error']['no'] === 0){
			$this->redirect($this->pre.'userinfo/form?uid='.$res['data']['uid'].'&token='.$res['data']['token']);
			return;
		}
	}
	
	public function saveAction(){
		$data = $this->getParams();
		$data['desc'] .= $data['desc2'];
		$data['address'] .= $data['address2'];
		$data['sex'] = intval($data['sex']);
		$token = $data['token'];
		unset($data['nickname']);
		unset($data['desc2']);
		unset($data['address2']);
		unset($data['address2']);
		unset($data['token']);
		$model = new userModel();
		$user = $model->get($data['uid']);
		if(empty($user)){
			$id = $model->add($data);
		}else{
			$id = $model->update($data);
		}
		$this->redirect($this->pre.'userinfo/form?uid='.$data['uid'].'&token='.$token);
	}
}
