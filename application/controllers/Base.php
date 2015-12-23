<?php

/**
 * filname      Base.php
 * author       jinxin
 * Description  Description of Base
 * Date         2014-9-15 14:40:04
 */

/**
 * Description of Base
 *
 * @author jinxin
 */
session_start();
class BaseController extends \SlatePF\Extras\ExtrasController {
	public $mailApi;
	protected $mailAuth;
	public $api;
	public $httpRequest;
	public $curPage;
	public $pagesize;

	public $ret_msg = [
		200 => '成功',
		100 => '数据不完整',
		101 => '用户不存在',
		102 => '操作失败',
		103 => '',
		104 => '活动不存在或者已经结束',

		201 => '用户已经抽过奖',
		202 => '需要完善信息',
		203 => '用户可以抽第二次奖'
	];
	public function init(){
		$config = Yaf\Application::app()->getConfig();
		$this->mailApi = $config->api['mail'];
		$this->curPage = $this->getParam('page',1);
		$this->pagesize = 20;
		$this->httpRequest = new \GuzzleHttp\Client();
		$this->api = $config->api;
		file_put_contents('/tmp/public_active.log',"接口：".$_SERVER['HTTP_REFERER']."==数据Post：".var_export($_POST,1)."==数据get：".var_export($_GET,1)."\r\n",FILE_APPEND);
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

	public function is_post(){
		if(strtolower($_SERVER['REQUEST_METHOD']) == 'post')
			return true;
		else
			return false;
	}

	public function error($data){
		return array('status'=>'error','data'=>$data);
	}
	
	public function success($data){
		return array('status'=>'success','data'=>$data);
	}
	
	public function displayError($msg){
		$this->assign('msg',$msg);
		return $this->display('error/error.twig');
	}

	public function returnJson( $code = 200,$data = '', $msg = ''){
		if($msg == ''){
			$msg = $this->ret_msg[$code];
		}
		$ret = array('code'=>$code,'data'=>$data, 'msg'=>$msg);
		file_put_contents('/tmp/public_active.log',"接口返回数据：".var_export($ret,1)."\r\n",FILE_APPEND);
		echo json_encode($ret);
		exit();
	}
}
