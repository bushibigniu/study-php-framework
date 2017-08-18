<?php

namespace Framework\Handles\Router;


use Framework\App;

/**
 * Class Job
 * job 路由处理机制
 */
class Job
{

    /**
     * @var
     *
     * 框架实例
     */
    private $app;


    /**
     * @var
     *
     * 配置实例
     */
    private $config;


    /**
     * 注册路由处理机制
     *
     * @param App $app 框架实例
     */
    public function register(App $app)
    {
        $this->app = $app;
        $this->app->notOutput = true;


        $this->config = $app::$container->getSingle('config');
        $this->routeStrategy = 'general';

        //开启路由
        $this->route();
    }

    /**
     * @param  路由机制
     */
    public function route()
    {
        // 路由策略
        $strategy = $this->routeStrategy;
        $this->$strategy();
        // 判断模块存不存在
        if (! in_array(strtolower($this->moduleName), $this->config->config['module'])) {
            throw new CoreHttpException(404, 'Module:'.$this->moduleName);
        }
        // 获job类
        $jobName = ucfirst($this->jobName);
        $jobPath = "Jobs\\{$this->moduleName}\\{$jobName}";
        // 判断控制器存不存在
        if (! class_exists($jobPath)) {
            throw new CoreHttpException(404, 'Job:'.$jobName);
        }
        // 反射解析当前控制器类　判断是否有当前操作方法
        $reflaction     = new ReflectionClass($jobPath);
        if (!$reflaction->hasMethod($this->actionName)) {
            throw new CoreHttpException(404, 'Action:'.$this->actionName);
        }
        // 实例化当前控制器
        $job = new $jobPath();
        // 调用操作
        $actionName = $this->actionName;
        // 获取返回值
        $this->app->responseData = $job->$actionName();
    }

    /**
     * 普通路由　url路径
     *
     * @param void
     */
    public function general()
    {
        $app            = $this->app;
        $request        = $app::$container->getSingle('request');
        $moduleName     = $request->request('module');
        $jobName        = $request->request('job');
        $actionName     = $request->request('action');
        $this->moduleName = $moduleName;
        $this->jobName    = $jobName;
        $this->actionName = $actionName;
    }
}