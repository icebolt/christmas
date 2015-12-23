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
		203 => '用户可以抽第二次奖',
		204 => '用户需要邀请更多好友才能再次抽奖',
		205 => '用户二次抽奖已经完成'
	];
	public function init(){
		$config = Yaf\Application::app()->getConfig();
		$this->mailApi = $config->api['mail'];
		$this->curPage = $this->getParam('page',1);
		$this->pagesize = 20;
		$this->httpRequest = new \GuzzleHttp\Client();
		$this->api = $config->api;
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
		echo json_encode(array('code'=>$code,'data'=>$data, 'msg'=>$msg));
		exit();
	}


	//
	/**
	 * 获取输入参数 支持过滤和默认值
	 * 使用方法:
	 * <code>
	 * I('id',0); 获取id参数 自动判断get或者post
	 * I('post.name','','htmlspecialchars'); 获取$_POST['name']
	 * I('get.'); 获取$_GET
	 * </code>
	 * @param string $name 变量的名称 支持指定类型
	 * @param mixed $default 不存在的时候默认值
	 * @param mixed $filter 参数过滤方法
	 * @param mixed $datas 要获取的额外数据源
	 * @return mixed
	 */
	function I($name,$default='',$filter=null,$datas=null) {
		static $_PUT	=	null;
		if(strpos($name,'/')){ // 指定修饰符
			list($name,$type) 	=	explode('/',$name,2);
		}elseif(C('VAR_AUTO_STRING')){ // 默认强制转换为字符串
			$type   =   's';
		}
		if(strpos($name,'.')) { // 指定参数来源
			list($method,$name) =   explode('.',$name,2);
		}else{ // 默认为自动判断
			$method =   'param';
		}
		switch(strtolower($method)) {
			case 'get'     :
				$input =& $_GET;
				break;
			case 'post'    :
				$input =& $_POST;
				break;
			case 'put'     :
				if(is_null($_PUT)){
					parse_str(file_get_contents('php://input'), $_PUT);
				}
				$input 	=	$_PUT;
				break;
			case 'param'   :
				switch($_SERVER['REQUEST_METHOD']) {
					case 'POST':
						$input  =  $_POST;
						break;
					case 'PUT':
						if(is_null($_PUT)){
							parse_str(file_get_contents('php://input'), $_PUT);
						}
						$input 	=	$_PUT;
						break;
					default:
						$input  =  $_GET;
				}
				break;
			case 'path'    :
				$input  =   array();
				if(!empty($_SERVER['PATH_INFO'])){
					$depr   =   C('URL_PATHINFO_DEPR');
					$input  =   explode($depr,trim($_SERVER['PATH_INFO'],$depr));
				}
				break;
			case 'request' :
				$input =& $_REQUEST;
				break;
			case 'session' :
				$input =& $_SESSION;
				break;
			case 'cookie'  :
				$input =& $_COOKIE;
				break;
			case 'server'  :
				$input =& $_SERVER;
				break;
			case 'globals' :
				$input =& $GLOBALS;
				break;
			case 'data'    :
				$input =& $datas;
				break;
			default:
				return null;
		}
		if(''==$name) { // 获取全部变量
			$data       =   $input;
			$filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
			if($filters) {
				if(is_string($filters)){
					$filters    =   explode(',',$filters);
				}
				foreach($filters as $filter){
					$data   =   array_map_recursive($filter,$data); // 参数过滤
				}
			}
		}elseif(isset($input[$name])) { // 取值操作
			$data       =   $input[$name];
			$filters    =   isset($filter)?$filter:C('DEFAULT_FILTER');
			if($filters) {
				if(is_string($filters)){
					if(0 === strpos($filters,'/')){
						if(1 !== preg_match($filters,(string)$data)){
							// 支持正则验证
							return   isset($default) ? $default : null;
						}
					}else{
						$filters    =   explode(',',$filters);
					}
				}elseif(is_int($filters)){
					$filters    =   array($filters);
				}

				if(is_array($filters)){
					foreach($filters as $filter){
						if(function_exists($filter)) {
							$data   =   is_array($data) ? array_map_recursive($filter,$data) : $filter($data); // 参数过滤
						}else{
							$data   =   filter_var($data,is_int($filter) ? $filter : filter_id($filter));
							if(false === $data) {
								return   isset($default) ? $default : null;
							}
						}
					}
				}
			}
			if(!empty($type)){
				switch(strtolower($type)){
					case 'a':	// 数组
						$data 	=	(array)$data;
						break;
					case 'd':	// 数字
						$data 	=	(int)$data;
						break;
					case 'f':	// 浮点
						$data 	=	(float)$data;
						break;
					case 'b':	// 布尔
						$data 	=	(boolean)$data;
						break;
					case 's':   // 字符串
					default:
						$data   =   (string)$data;
				}
			}
		}else{ // 变量默认值
			$data       =    isset($default)?$default:null;
		}
		is_array($data) && array_walk_recursive($data,'think_filter');
		return $data;
	}

	function array_map_recursive($filter, $data) {
		$result = array();
		foreach ($data as $key => $val) {
			$result[$key] = is_array($val)
				? array_map_recursive($filter, $val)
				: call_user_func($filter, $val);
		}
		return $result;
	}

}
