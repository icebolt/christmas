<?php

/**
 * filname      sn.php
 * author       jinxin
 * Description  Description of sn
 * Date         2014-9-27 15:08:07
 */

/**
 * Description of sn
 *
 * @author jinxin
 */
class snModel extends baseModel {
	public function __construct() {
		parent::__construct();
	}
	
	public function getSn($life){
		$sql = $this->query()->select('*')->from('app_series_num')->where("used=0 and life={$life} AND endtime > {$_SERVER['REQUEST_TIME']}")->build();
		$row = $this->db->getRow($sql);
		return $row;
	}
	public function updateSnStatus($id,$status){
		return $this->db->update('app_series_num',array('used'=>$status),'id='.$id);
	}
	public function updateThirdSnStatus($id,$status){
		return $this->db->update('third_sn',array('used'=>$status),'id='.$id);
	}
	public function getThirdSnById($id){
		$sql = $this->query()->select('*')->from('third_sn')->where("id={$id}")->build();
		$row = $this->db->getRow($sql);
		return $row;
	}
	public function getThirdSn($type){
		$sql = $this->query()->select('*')->from('third_sn')->where("type='{$type}' AND used=0")->build();
		$row = $this->db->getRow($sql);
		return $row;
	}
	
	public function addLog($data){
		$id = $this->db->insert('getsn_log',$data);
		return $id;
	}
}
