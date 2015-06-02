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
	public $httpRequest;
	public function __construct() {
		$this->db = \SlatePF\Database\Db::getInstance();
		$this->httpRequest = new \GuzzleHttp\Client();
	}
	
	
	/**
	 * http request function
	 * @param string $url
	 * @param array $postdata
	 * @param string $dataype
	 * @return array('status'=>'','data'=>array(),'context'=>'context')
	 */
	public function getHttpResponse($url,$postdata=array(),$dataype='json'){
		$post = array();
		try{
			fb($url);
			if(empty($postdata)){
				$res = $this->httpRequest->get($url,array(),array('timeout'=> 20,'connect_timeout' => 3.5));
			}else{
				$post = array('body'=>[$postdata]);
				fb($post);
				$res = $this->httpRequest->post($url,$post,array('timeout'=> 20,'connect_timeout' => 3.5));
			}
			switch ($dataype) {
				case 'json':
					try{
						$data = $this->success($res->json());
					}catch (Exception $e){
						ob_start();
						echo $res->getBody();
						$response = ob_get_contents();
						ob_end_clean();
						$data = $this->error(array('msg'=>$e->getMessage(),'response'=>$response));
					}
					break;
				default:
					ob_start();
					echo $res->getBody();
					$response = ob_get_contents();
					ob_end_clean();
					$data = array('status'=>'success','context'=>$response);
			}
			return $data;
		}catch (\GuzzleHttp\Exception\RequestException $e){
			$this->httpErr = $this->error(array(
				'type'=>'httpError',
				'code'=>$e->getCode(),
				'uri'=>$url,
				'msg'=>$e->getMessage(),
				'post'=>$post,
				'request'=>$e->getRequest()));
			return $this->httpErr;
		}
	}
	
	public function error($data){
		return array('status'=>'error','data'=>$data);
	}
	
	public function success($data){
		return array('status'=>'success','data'=>$data);
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
