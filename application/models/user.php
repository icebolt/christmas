<?php

/**
 * filname      user.php
 * author       jinxin
 * Description  Description of user
 * Date         2015-5-28 17:15:39
 */

/**
 * Description of user
 *
 * @author jinxin
 */
class userModel extends baseModel {
	public $tableName;
	public function __construct() {
		parent::__construct();
		$this->tableName = 'user_log';
	}
	
	public function get($id){
		$sql = $this->query()->select('*')->from($this->tableName)->where("id={$id}")->build();
		return $this->db->getRow($sql);
	}
	
	public function find($param){
		$res = $this->fatch($this->tableName,$param);
		return $res[0];
	}
	
	public function getUserBaseInfo($uid){
		$user = array();
		$config = Yaf\Application::app()->getConfig();
		$api = $config->api['user'].'getUserInfo&datatype=2';
		$api .= "&uids={$uid}";
		$res = $this->getHttpResponse($api);
		if(is_array($res['data']['user']) && !empty($res['data']['user'])){
			$user = $res['data']['user'][0];
		}
		return $user;
	}

	public function getByUid($params){
		return $this->fatch($this->tableName, $params);
	}

	public function add($data){
		return $this->db->insert($this->tableName,$data);
	}
	
	public function update($data){
		$data['data'] = $this->db->escape($data['data']);
		return $this->db->update($this->tableName,$data,'id='.$data['id']);
	}
	
}
