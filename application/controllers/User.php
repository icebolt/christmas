<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/23
 * Time: 下午3:21
 */
class UserController extends BaseController
{
    /**
     * 活动ID
     * @var int
     */
    private $active_id = 0;

    /**
     * opend_id
     * 微信唯一ID等
     */
    private $open_id = '';
    /**
     * 邀请人ID
     * @var int
     */
    private $inviter_id = 0;
    /**
     * 登录类型 0 html5,1微信，2新浪，3QQ'
     * @var int
     */
    private $type = 0;
    /**
     * 用户MODEL
     */

    /**
     * 随机数
     */
    private $rand_string = 0;
    public function init()
    {
        parent::init();


        $active_id = I('active_id');
        $type = I('type', 0);
        if(!$active_id){
            $this->returnJson(100);
        }
        $this->active_id = $active_id;
        $this->type = $type;
        $this->rand_string = rand(10000,99999);
    }
    public function indexAction()
    {
        echo 123;
    }
    public function loginAction()
    {
        $activeUserModel = new activeUserModel();
        $open_id = I('open_id');
        $this->inviter_id = I('inviter_id', 0, 'intval');
        if(!$open_id){
            $this->returnJson(100);
        }
        $this->open_id = $open_id;
        switch($this->type){
            case 0:
                $this->_webLogin();
                break;
            case 1:
                $this->_wenxinLogin();
                break;
            case 2:
                $this->_sinaLogin();
                break;
            case 3:
                $this->_qqLogin();
                break;
            default:
                $this->returnJson(102);
        }

    }

    /**
     * 添加用户信息
     */
    public function addinfoAction()
    {
        $uid = I('uid', 0, 'intval');
        $token = I('token');
        $this->CheckUser($uid, $token);
        $activeUserModel = new activeUserModel();
        $content = I('content');
        if(is_array($content)){
            $content = json_encode($content,JSON_UNESCAPED_UNICODE);
        }
        $ret = $activeUserModel->editInfo($uid, $content);
        if($ret){
            $this->returnJson(200);
        }else{
            $this->returnJson(102);
        }
    }

    /**
     * 判断是否用户合法
     */
    private function CheckUser($uid, $token)
    {
        $userModel = new activeUserModel();
        $userInfo = $userModel->getUserInfo($uid);
        $local_token = md5($userInfo['id'].'@'. $userInfo['open_id'].'@'.$userInfo['type'].'@'.$userInfo['rand_string']);
        if($local_token != $token){
            $this->returnJson(101);
        }
        return true;
    }

    /**
     * web登录
     */
    private function _webLogin()
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
            $ret = $this->_regUser();
            if($ret){
                $token = md5($ret.'@'. $this->open_id.'@'.$this->type.'@'.$this->rand_string);
                $uid = $ret;
                $content ='';
            }
        }
        if($token && $uid){
            $userArr['uid'] = $uid;
            $userArr['token'] = $token;
            $userArr['content'] = $content;
            $this->returnJson(200, $userArr);
        }else{
            $this->returnJson(102);
        }
    }

    /**
     * 微信登录
     */
    private function _wenxinLogin()
    {
        $retArr = $this->_getToken();
        $this->open_id = $retArr["openid"];
        //判断用户是否已经存在
        $userInfo = $this->_isUser();
        if($userInfo){
            //生成token规则
            //token = md5(uid@open_id@active_id@type@rand_string)
            $token = md5($userInfo['id'].'@'. $userInfo['open_id'].'@'.$userInfo['type'].'@'.$userInfo['rand_string']);
            $uid = $userInfo['id'];
            $content = $userInfo['content'];
        }else{
            $weixinInfo = $this->_getUserInfo($retArr['access_token']);
            var_dump($weixinInfo);
            $data = [
                'content'=>json_encode($weixinInfo, JSON_UNESCAPED_UNICODE),
                'nickname'=>$weixinInfo['nickname']
            ];
            //注册
            $ret = $this->_regUser($data);
            if($ret){
                $token = md5($ret.'@'. $this->open_id.'@'.$this->type.'@'.$this->rand_string);
                $uid = $ret;
                $content ='';
            }
        }
        if($token && $uid){
            $userArr['uid'] = $uid;
            $userArr['token'] = $token;
            $userArr['content'] = $content;
            $this->returnJson(200, $userArr);
        }else{
            $this->returnJson(102);
        }
    }

    /**
     * sina登录
     */
    private function _sinaLogin()
    {

    }

    /**
     * qq登录
     */
    private function _qqLogin()
    {

    }

//========================================================
    /**
     * 用户是否存在
     * @return mixed
     */
    private function _isUser()
    {
        //是 返回true 不是返回 false
        $activeUserModel = new activeUserModel();
        return $userinfo = $activeUserModel->getUser($this->active_id,$this->type, $this->open_id);
    }

    /**
     * 注册用户
     * @return mixed
     */
    private function _regUser($addinfo = []){
        $data = [];
        $data['inviter_id'] = $this->inviter_id;
        $data['active_id'] = $this->active_id;
        $data['open_id'] = $this->open_id;
        $data['type'] = $this->type;
        $data['rand_string'] = $this->rand_string;
        if($data){
            //微信昵称等信息
            foreach($addinfo as $k => $v){
                $data[$k] = $v;
            }

        }
        file_put_contents('/tmp/newactive.log',"注册：".var_export($data, 1)."\r\n",FILE_APPEND);
        $activeUserModel = new activeUserModel();
        return $user = $activeUserModel->addUser($data);
    }


    /**
     * 获取weixin token
     * @return array
     * https://open.weixin.qq.com/connect/oauth2/authorize
     * ?appid=wxb7f74b81b6e5ef76
     * &redirect_uri=http%3A%2F%2Fsecret-ajax.hortor.net%2Fimpress%2F%3Fshare_code%3Dobk6yuDrbSrCvjCBBax32C33S7q8
     * &response_type=code&scope=snsapi_userinfo&state=data#wechat_redirect
     * https://open.weixin.qq.com/connect/oauth2/authorize
     * ?appid=APPID&redirect_uri=REDIRECT_URI&response_type=code&scope=SCOPE&state=STATE#wechat_redirect
     */
    private function _getToken(){
        $appid = C("weixin.appid");
        $secret = C("weixin.appsecret");
        $url ="https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$this->open_id&grant_type=authorization_code";
        $ret = $this->getHttpResponse($url);
        if($ret['status'] = 'success'){
            return $ret['data'];
        }else{
            $this->returnJson(102);
        }
    }
    /**
     * 获取用户信息
     * @param  $access_token
     * @return array
     */
    private function _getUserInfo($access_token){
        echo $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$this->open_id";
        $ret = $this->getHttpResponse($url);
        if($ret['status'] = 'success'){
            return $ret['data'];
        }else{
            $this->returnJson(102);
        }
    }


}
