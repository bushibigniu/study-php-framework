<?php

namespace Framework;

use Closure;
use Framework\Exceptions\CoreHttpException;

/**
 * Application
 *
 * 框架应用类
 *
 * 整个框架自身就是一个应用
 *
 */
class App
{
    /**
     * 框架加载流程一系列处理类集合
     * @var array
     */
    private $handleList = [];

    /**
     * 请求对象
     *
     * @var object
     */
    private $request;

    /**
     * 响应对象
     *
     * @var object
     */
    private $responseData;

    /**
     * 框架实例根目录
     *
     * @var
     */
    private $rootPath;

    /**
     * cli模式
     *
     * @var string
     */
    private $isCli = 'false';

    /**
     * 是否输出响应结果
     *
     * 默认输出
     *
     * cli模式　访问路径为空　不输出
     *
     * @var bool
     */
    private $notOutput = false;

    /**
     * 框架实例
     *
     * @var object
     */
    public static $app;

    /**
     * 服务容器
     *
     * @var object
     */
    public static $container;


    /**
     * 构造函数
     *
     * @param  string $rootPath 框架实例根目录
     * @param  string $loader   注入自加载实例
     */
    public function __construct($rootPath, Closure $loader)
    {
        // 根目录
        $this->rootPath = $rootPath;

        // cli模式
        $this->isCli = getenv('IS_CLI');

        // 注册自加载
        $loader();

        Load::register($this);

        self::$app = $this;
        self::$container = new Container();
    }

    /**
     * 魔法函数__get
     *
     * @param  string $name  属性名称
     * @return mixed
     */
    public function __get($name = '')
    {
        return $this->$name;
        // TODO: Implement __get() method.
    }

    /**
     * 魔法函数__set
     *
     * @param  string $name   属性名称
     * @param  mixed  $value  属性值
     * @return mixed
     */
    public function __set($name = '', $value = '')
    {
        return $this->$name = $value;
        // TODO: Implement __set() method.
    }

    /**
     * 注册框架运行过程中一系列处理类
     *
     * @param  object $handle handle类
     * @return void
     */
    public function load(Closure $handle)
    {
        $this->handleList[] = $handle;
    }

    /**
     * 内部调用get
     *
     * 可构建微单体架构
     *
     * @param  string $uri 要调用的path
     * @param  array $argus 参数
     * @return void
     */
    public function get($uri = '', $argus = array())
    {
        return $this->callSelf('get', $uri, $argus);
    }

    /**
     * 内部调用post
     *
     * 可构建微单体架构
     *
     * @param string $uri path
     * @param array $argus 参数
     * @return void
     */
    public function post($uri = '', $argus = array())
    {
        return $this->callSelf('post', $uri, $argus);
    }

    /**
     * 内部调用put
     *
     * 可构建微单体架构
     *
     * @param string $uri path
     * @param array $argus 参数
     * @return void
     */
    public function put($uri = '', $argus = array())
    {
        return $this->callSelf('put', $uri, $argus);
    }

    /**
     * 内部调用delete
     *
     * 可构建微单体架构
     *
     * @param string $uri path
     * @param array $argus 参数
     * @return void
     */
    public function delete($uri = '', $argus = array())
    {
        return $this->callSelf('delete', $uri, $argus);
    }

    /**
     *
     * 内部调用
     *
     * 可构建微单体架构
     *
     * @param string $method    模拟的http请求method
     * @param string $uri       要调用的path
     * @param array $argus      参数
     * @return json
     */
    public function callSelf($method = '', $uri = '', $argus = array())
    {
        $requestUri = explode('/', $uri);
        if (count($requestUri) !== 3) {
            throw new CoreHttpException(400);
        }
        $request = self::$container->getSingle('request');
        $request->method = $method;
        $request->requestParams = $argus;
        $request->getParams = $argus;
        $request->postParams = $argus;
        $router = self::$container->getSingle('route');
        $router->moduleName = $requestUri[0];
        $router->controllerName = $requestUri[1];
        $router->actionName = $requestUri[2];
        $router->routeStrategy = 'microMonomer';
        $router->route();
        return $this->responseData;
    }

    /**
     * 运行应用
     *
     * @param  Request $request 请求对象
     * @return void
     */
    public function run(Closure $request)
    {
        self::$container->getSingle('request', $request);
        foreach ($this->handleList as $handle) {
            $instande = $handle();
            self::$container->setSingle(get_class($instande), $instande);
            $instande->register($this);
        }
    }

    /**
     * 生命周期结束
     *
     * 响应请求
     * @param  Closure $closure 响应类
     * @return json
     */
    public function response(Closure $closure)
    {
        if ($this->notOutput === true) {
            return;
        }

        if ($this->isCli === 'yes') {
            $closure()->cliModeSuccess($this->responseData);
            return;
        }
        $useRest = self::$container->getSingle('config')->config(['rest_response']);

        if ($useRest) {
            $closure()->restSuccess($this->responseData);
        }
        $closure()->response($this->responseData);
    }
}
