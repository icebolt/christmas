<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 15/12/23
 * Time: 下午3:21
 */
class UserController extends BaseController
{
    public function init()
    {
        parent::init();
    }

    public function loginAction()
    {
        $type = $_GET['type'];
    }

}