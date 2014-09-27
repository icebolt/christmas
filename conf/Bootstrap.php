<?php

/**
 * filname      Bootstrap.php
 * author       jinxin
 * Description  Description of Bootstrap
 * Date         2014-3-19 18:23:20
 */

/**
 * Description of Bootstrap
 *
 * @author jinxin
 */
define('MYSQL_LOG_ERROR', true);
class Bootstrap extends \Yaf\Bootstrap_Abstract{
    protected $config;
    protected $lib;
    public function _initConfig(Yaf\Dispatcher $dispatcher) {
        //把配置保存起来
        $this->config = Yaf\Application::app()->getConfig();
        Yaf\Registry::set('config', $this->config);		

        define('REQUEST_METHOD', strtoupper($dispatcher->getRequest()->getMethod()));
        $this->lib = ini_get("yaf.library");
        Yaf\Registry::set('yafpath', $this->lib);
		Yaf\Registry::set('localLibrary', $this->config->application->library);
        Yaf\Loader::import($this->lib."/SlatePF/Function.php");
		L("vendor/autoload.php");
    }

    public function _initPlugin(Yaf\Dispatcher $dispatcher) {
        //注册一个插件
//        $objSamplePlugin = new SamplePlugin();
//        $dispatcher->registerPlugin($objSamplePlugin);
    }

    public function _initRoute(Yaf\Dispatcher $dispatcher) {
            //$router = $dispatcher->getRouter();
            // default yaf rewrite route
            //$router->addRoute('dog', new \Yaf\Route\Rewrite('dog/:id', array('controller' => 'dog', 'action' => 'read')));
            //$router->addRoute('dogadd', new \Yaf\Route\Rewrite('dog', array('controller' => 'dog', 'action' => 'create')));
            //$router->addRoute('dogdel', new \Yaf\Route\Rewrite('dog/:id/delete', array('controller' => 'dog', 'action' => 'delete')));
    }

    private function _initRESTfulRoute() {
        if (REQUEST_METHOD != 'CLI'){
            $router = new SlatePF\Extras\RESTfulRouter();
//            $router->on('post', 'mail', 'index', 'add');
//            $router->on('get', 'mail', 'index', 'get');
//            $router->on('put', 'mail', 'index', 'list');
//            $router->on('get post', 'dog/:name', 'dog', 'yeah');
//            $router->on('*', 'pig/:id', 'pig', 'eat');		
        }
    }

    public function _initController(){
        Yaf\Loader::import($this->lib."/SlatePF/Extras/ExtrasController.php");
    }

    public function _initView(Yaf\Dispatcher $dispatcher){
        $view = new \SlatePF\Extras\ExtrasView();
        $path = $view->getScriptPath();
        // plain text TEST
        $view->on('txt', function ($file, $data) use ($path) {
            return file_get_contents( $path .$file ) .' THIS IS JUST A TEST';
        });

        // twig
        $view->on('twig', function ($file, $data) use ($path) {
            $loader = new Twig_Loader_Filesystem($path);
            $twig = new Twig_Environment($loader);
            return $twig->loadTemplate($file)->render($data);
        });

        //protobuf
        $view->on('pb', function ($file, $data) use ($path) {
            include( $path .$file);
            exit();
        });		


        $dispatcher->disableView(); // disable auto-render
        $dispatcher->setView($view);		
    }

    public function _initDatabase(Yaf\Dispatcher $dispatcher) {
        $servers = array();
        $database = $this->config->database;
        $servers[] = $database->master->toArray();
        $slaves = $database->slaves;
        if (!empty($slaves))
        {
            $slave_servers = explode('|', $slaves->servers);
            $slave_users = explode('|', $slaves->users);
            $slave_passwords = explode('|', $slaves->passwords);
            $slave_databases = explode('|', $slaves->databases);
            $slaves = array();
            foreach ($slave_servers as $key => $slave_server)
            {
                if (isset($slave_users[$key]) && isset($slave_passwords[$key]) && isset($slave_databases[$key]))
                {
                    $slaves[] = array('server' => $slave_server, 'user' => $slave_users[$key], 'password' => $slave_passwords[$key], 'database' => $slave_databases[$key]);
                }
            }
            $servers[] = $slaves[array_rand($slaves)];
        }
        Yaf\Registry::set('database', $servers);
        if (isset($database->mysql_cache_enable) && $database->mysql_cache_enable && !defined('MYSQL_CACHE_ENABLE'))
        {
            define('MYSQL_CACHE_ENABLE', true);
        }
        if (isset($database->mysql_log_error) && $database->mysql_log_error && !defined('MYSQL_LOG_ERROR'))
        {
            define('MYSQL_LOG_ERROR', true);
        }
    }


    public function _initJob(Yaf\Dispatcher $dispatcher) {

    }
}
