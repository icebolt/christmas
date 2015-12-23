<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/23
 * Time: 下午3:21
 */
class UserroleController extends BaseController
{
    public function init()
    {
        parent::init();
    }

    public function loginAction()
    {
        $state = htmlspecialchars($_GET['state']);
        if(!$state ==$_SESSION['active']['state']){
            echo "不要攻击我，我会报警的！";
            exit;
        }
        $active_id = intval($_GET['active_id']);
        $type = intval($_GET['type']);
        $inviter_id = @intval($_GET['inviter_id']);

        if(!isset($active_id) || !isset($type) || !$type || !$active_id){
            $this->returnJson(100);
        }
        $ret = $this->_getToken();
        if(!isset($ret['access_token'])){
            $this->returnJson(100);
        }
        //token 去获取用户昵称
        $userInfo = $this->_getUserInfo($ret['access_token'],$ret['openid']);
        if(!isset($userInfo['nickname'])){
            $this->returnJson(100);
        }
        $this->open_id = $ret['openid'];
        $this->type = $type;
        $this->inviter_id = $inviter_id;
        $this->active_id = $active_id;
        if($this->login()){
            $url = $this->host_url ."active/Christmas/index.php";
            header("location: $url");
        }
    }

    /**
     * test
     */
    public function testAction(){
        $open_id = htmlspecialchars($_GET['open_id']);
        $type = intval($_GET['type']);
        $active_id = intval($_GET['active_id']);
        $inviter_id = $_GET['inviter_id']?intval($_GET['inviter_id']):0;
        if(!isset($open_id) || !isset($type) || !isset($active_id) || !$active_id || !$type || !$open_id){
            $this->returnJson(100);
        }
        $this->open_id = $open_id;
        $this->type = $type;
        $this->inviter_id = $inviter_id;
        $this->active_id = $active_id;

//        $this->open_id = $_GET['open_id'];
//        $this->type = $_GET['type'];
//        $this->inviter_id = @$_GET['inviter_id'];
//        $this->active_id = $_GET['active_id'];
        $this->referer = !empty($_GET['referer'])?$_GET['referer']:"index";

        $result=$this->_login();
        if($result!==false){
            $_SESSION['uid'] = $result["uid"];
            $_SESSION['token'] = $result["token"];
            $_SESSION['content'] = $result["content"];
            $url = $this->host_url ."active/Christmas/".$this->referer.".php";
            header("location: $url");
        }
    }


    private function _login()
    {
        //判断用户是否已经存在
        $userInfo = $this->_isUser();
        if($userInfo){
            //生成token规则
            //token = md5(uid@open_id@active_id@type@rand_string)
            $token = md5($userInfo['id'].'@'. $userInfo['open_id'].'@'.$userInfo['type'].'@'.$userInfo['rand_string']);
            $uid = $userInfo['id'];
            $content = $userInfo['content'];
        }else{
            //注册
            $data = [];
            $data['inviter_id'] = $this->inviter_id ? $this->inviter_id : 0;
            $data['active_id'] = $this->active_id;
            $data['open_id'] = $this->open_id;
            $data['type'] = $this->type;
            $ret = $this->_regUser($data);
            if($ret){
                $rand_string = $this->activeUserModel->getRandNum();
                $token = md5($ret.'@'. $data['open_id'].'@'.$data['type'].'@'.$rand_string);
                $uid = $ret;
                $content ='';
            }
        }
        if($token && $uid){
            $userArr['uid'] = $uid;
            $userArr['token'] = $token;
            $userArr['content'] = $content;
            return $userArr;
        }else{
            return false;
        }
    }

    public function checkAction(){
        $open_id = htmlspecialchars($_GET['open_id']);
        $type = intval($_GET['type']);
        $active_id = intval($_GET['active_id']);
        $inviter_id = $_GET['inviter_id']?intval($_GET['inviter_id']):0;
        if(!isset($open_id) || !isset($type) || !isset($active_id) || !$active_id || !$type || !$open_id){
            $this->returnJson(100);
        }
        $this->open_id = $open_id;
        $this->type = $type;
        $this->inviter_id = $inviter_id;
        $this->active_id = $active_id;

        $result=$this->_login();
        if($result!==false){
            $result['content'] = json_decode($result["content"],true);
            echo json_encode($result);
        }else{
            $result["error"]["errorno"]=100;
            $result["error"]["errormessage"]="系统错误";
            echo json_encode($result);
        }
    }


    private function _isUser()
    {
        //是 返回true 不是返回 false
        return $userinfo = $this->activeUserModel->getUser($this->active_id,$this->type, $this->open_id);
    }
    private function _regUser($data){
        return $user = $this->activeUserModel->addUser($data);
    }

    private function _getToken(){
        $config = Yaf\Application::app()->getConfig();
        $appid = $config->weixin["appid"];
        $secret = $config->weixin["appsecret"];
        $code = htmlspecialchars($_GET['code']);
        $url ="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";
        return $ret = $this->getHttpResponse($url);
    }
    /**
     * 获取用户信息
     * @param $access_token
     * @param $openid
     * @return array
     */
    private function _getUserInfo($access_token, $openid){
        $url = "https://api.weixin.qq.com/sns/auth?access_token=$access_token&openid=$openid";
        $ret = $this->getHttpResponse($url);
        return $ret;
    }

    public function logoutAction(){
        session_unset();
        session_destroy();
    }


}
