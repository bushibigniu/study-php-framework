<?php

namespace Framework\Handles;

use Framework\App;

class RouterHandle implements Handle
{

    /**
     * 自定义路由规则
     *
     * get请求
     *
     * 查询数据
     *
     * @var array
     */
    private $getMap = [];
    /**
     * 自定义路由规则
     *
     * post请求
     *
     * 新增数据
     *
     * @var array
     */
    private $postMap = [];
    /**
     * 自定义路由规则
     *
     * put请求
     *
     * 更新数据
     *
     * @var array
     */
    private $putMap = [];
    /**
     * 自定义路由规则
     *
     * delete请求
     *
     * 删除数据
     *
     * @var array
     */
    private $deleteMap = [];

    public function register(App $app)
    {
        // TODO: Implement register() method.
    }


    /**
     * @param string $name
     * @return mixed
     */

    public function __get($name = '')
    {
        return $this->$name;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set($name = '', $value = '')
    {
        $this->$name = $value;
    }


    /**
     * 自定义get请求路由
     *
     * @param  string $uri      请求uri
     * @param  mixed  $function 匿名函数或者控制器方法标示
     * @return void
     */
    public function get($uri = '', $function = '')
    {
        $this->getMap[$uri] = $function;
    }
    /**
     * 自定义post请求路由
     *
     * @param  string $uri      请求uri
     * @param  mixed  $function 匿名函数或者控制器方法标示
     * @return void
     */
    public function post($uri = '', $function = '')
    {
        $this->postMap[$uri] = $function;
    }
    /**
     * 自定义put请求路由
     *
     * @param  string $uri      请求uri
     * @param  mixed  $function 匿名函数或者控制器方法标示
     * @return void
     */
    public function put($uri = '', $function = '')
    {
        $this->putMap[$uri] = $function;
    }
    /**
     * 自定义delete请求路由
     *
     * @param  string $uri      请求uri
     * @param  mixed  $function 匿名函数或者控制器方法标示
     * @return void
     */
    public function delete($uri = '', $function = '')
    {
        $this->deleteMap[$uri] = $function;
    }

}