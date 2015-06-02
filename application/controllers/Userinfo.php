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
		$model = new userModel();
		$action = $this->pre.'/userinfo/save';
		
		if(empty($appid) || empty($aid)){
			return $this->displayError('no require params');
		}
		
		$this->assign('action',$action);
		$this->assign('appid',$appid);
		$this->assign('aid',$aid);
		if(!empty($uid)){
			$userInfo = $model->getUserBaseInfo($uid);
			if(!empty($userInfo)){
				$data['uid'] = $userInfo['uid'];
				$data['nickname'] = $userInfo['nickname'];
				$data['avatar'] = $userInfo['avatar'];
				$extInfo = $model->getByUid(array('appid'=>$appid,'activeid'=>$aid,'uid'=>$userInfo['uid']));
				if(!empty($extInfo)){
					$extData = !empty($extInfo[0]['data'])?json_decode($extInfo[0]['data'],true):array();
					$data = array_merge($data,$extData);
				}
				$this->assign('id', $id);
				$this->assign('user', $data);
				$this->assign('action',$action);
				return $this->display('user/info.twig');
			}
			return $this->display('user/error.twig');
		}elseif(!empty ($id)){
			$user = $model->get($id);
			$userInfo = $model->getUserBaseInfo($user['uid']);
			$data = json_decode($user['data'],true);
			if(!empty($userInfo)){
				$data['nickname'] = $userInfo['nickname'];
				$data['avatar'] = $userInfo['avatar'];
			}
			$this->assign('id',$user['id']);
			$this->assign('user',$data);
			return $this->display('user/info.twig');
		}
		return $this->display('user/info.twig');
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
		$model = new userModel();
		if(!empty($data['uid'])){
			$param = array('appid'=>$data['appid'],'activeid'=>$data['aid'],'uid'=>$data['uid']);
		}elseif(!empty ($data['id'])){
			$param = array('id'=>$data['id']);
		}
		$data['address'] = addslashes(trim($data['address']));
		$data['desc'] = addslashes(trim($data['desc']));
		$user = $model->find($param);
		$updateData['appid'] = intval($data['appid']);
		$updateData['activeid'] = intval($data['aid']);
		$updateData['uid'] = intval($data['uid']);
		$updateData['type'] = empty($data['uid'])?0:1;
		$updateData['data'] = json_encode($data,JSON_UNESCAPED_UNICODE);
		if(empty($user)){
			$id = $model->add($updateData);
		}else{
			$updateData['id'] = $user['id'];
			$id = $model->update($updateData);
		}
		$row = $model->get($user['id']);
		if(!empty($id))
			$this->redirect($this->pre.'userinfo/form/appid/'.$data['appid'].'/aid/'.$data['aid'].'/id/'.$id);
	}
}
