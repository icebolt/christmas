<?php

/**
 * filname      base.php
 * author       jinxin
 * Description  Description of base
 * Date         2014-9-27 15:06:00
 */

/**
 * Description of base
 *
 * @author jinxin
 */
class baseModel {
	public $db;
	public function __construct() {
		$this->db = \SlatePF\Database\Db::getInstance();
	}
	
	public function query(){
		return new SlatePF\Database\DbQuery();
	}
	
	public function fatch($tablename,$where=array(),$fields='*'){
		$sqlHandle = $this->query()->select($fields)->from($tablename);
		foreach($where as $k=>$row){
			if(is_array($row)){
				$sqlHandle->where("`{$k}`{$row['condition']}{$row['value']}");
			}else{
				$sqlHandle->where("`{$k}`=\"{$row}\"");
			}
		}
		$sql = $sqlHandle->build();
		$this->lastsql = $sql;
		return $this->db->executeS($sql);
	}
}
