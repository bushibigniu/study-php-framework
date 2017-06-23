<?php


use Framework\Handles\ConfigHandle;
use Framework\Handles\LogHandle;
use Framework\Handles\ErrorHandle;
use Framework\Handles\ExceptionHandle;
use Framework\Handles\NosqlHandle;
use Framework\Handles\UserDefinedHandle;
use Framework\Handles\RouterHandle;
use Framework\Exceptions\CoreHttpException;
use Framework\Request;
use Framework\Response;

/**
 * 引入框架文件
 * Require framework
 */
require(__DIR__ .'App.php');


try {
    //------------------------------------------------------------------------//
    //                                  INIT                                  //
    //------------------------------------------------------------------------//

    /**
     * 初始化应用
     *
     * Init framework
     */
    $app = new \Framework\App(__DIR__ . '/..', function () {
        return require(__DIR__ . '/Load.php');
    });


    //-----------------------------------------------------------------------//
    //                         LOADING HANDLE MODULE                         //
    //-----------------------------------------------------------------------//
    $app->load(function () {
        //加载预定义配置机制 Loading config handle
        return new ConfigHandle();
    });

    $app->load(function () {
        //加载日志处理机制  Loading log handle
        return new LogHandle();
    });

    $app->load(function () {
        //加载错误处理机制
        return new ErrorHandle();
    });

    $app->load(function () {
        //加载异常处理机制
        return new ExceptionHandle();
    });

    $app->load(function () {
        //加载nosql处理机制
        return new NosqlHandle();
    });

    $app->load(function () {
        //加载用户自定义机制
        return new UserDefinedHandle();
    });

    $app->load(function () {
        //加载路由机制
        return new RouterHandle();
    });


    //-----------------------------------------------------------------------//
    //                              START APP                                //
    //-----------------------------------------------------------------------//

    /**
     * 启动应用
     *
     * Start framework
     */
    $app->run(function () use ($app) {
        return new Request($app);
    });

    //-----------------------------------------------------------------------//
    //                          STOP APP & RESPONSE                          //
    //-----------------------------------------------------------------------//

    /**
     * 响应结果
     *
     * Reponse
     *
     * 应用生命周期结束
     *
     * End
     */
    $app->run(function () use ($app) {
        return new Response();
    });
} catch (CoreHttpException $e) {
    /**
     * 捕获异常
     *
     * Catch exception
     */
    $e->reponse();
}
