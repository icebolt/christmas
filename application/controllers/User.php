<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/11
 * Time: 下午3:32
 */
class UserController extends BaseController
{
    private $active_id = 0;
    private $type = 0;
    private $open_id = 0;
    private $inviter_id = 0;
    private $activeUserModel = '';

    public function init()
    {
        $this->activeUserModel = new activeUserModel();
    }
    /**
     * 登陆/注册
     */
    public function loginAction()
    {
        //openid
        $open_id = htmlspecialchars($_POST['open_id']);
        $active_id = intval($_POST['active_id']);
        $type = intval($_POST['type']);
        $inviter_id = @intval($_POST['inviter_id']);
        if(!isset($active_id) || !isset($open_id) || !isset($type) || !$open_id || !$type || !$active_id){
            $this->returnJson(100);
        }
        $this->active_id = $active_id;
        $this->open_id = $open_id;
        $this->type = $type;
        //判断用户是否已经存在
        $userInfo = $this->_isUser();
        if($userInfo){
            //生成token规则
            //token = md5(uid@open_id@active_id@type@rand_string)
            $token = md5($userInfo['id'].'@'. $userInfo['open_id'].'@'.$userInfo['type'].'@'.$userInfo['rand_string']);
            $uid = $userInfo['id'];
        }else{
            //注册
            $data = [];
            $data['inviter_id'] = $inviter_id ? $inviter_id:$this->inviter_id;
            $data['active_id'] = $this->active_id;
            $data['open_id'] = $this->open_id;
            $data['type'] = $this->type;
            $ret = $this->_regUser($data);
            if($ret){
                $rand_string = $this->activeUserModel->getRandNum();
                $token = md5($ret.'@'. $data['open_id'].'@'.$data['type'].'@'.$rand_string);
                $uid = $ret;
            }
        }
        if($token && $uid){
            $data = ['token'=> $token,'uid'=>$uid];
            $this->returnJson(200, $data);
        }else{
            $this->returnJson(102);
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


}