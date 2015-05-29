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
		$this->tableName = 'user_ext';
	}
	
	public function get($id){
		$sql = $this->query()->select('*')->from($this->tableName)->where("id={$id}")->build();
		return $this->db->getRow($sql);
	}


	public function add($data){
		return $this->db->insert($this->tableName,$data);
	}
	
	public function update($data){
		return $this->db->update($this->tableName,$data,'uid='.$data['uid']);
	}
	
}
