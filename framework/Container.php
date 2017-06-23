<?php

namespace Framework;

use Framework\Exceptions\CoreHttpException;

/**
 * 依赖注入容器
 *
 * Dependency Injection Container
 *
 * 外观模式的典型代表
 *
 */
class Container
{
    /**
     * class intance 映射
     *
     * class instance map
     * @var array
     */
    public $instanceMap = [];

    /**
     * 映射类
     *
     * class map
     *
     * @var array
     */
    public $classMap = [];

    /**
     * 注入一个类
     *
     * inject a class
     *
     * @param string $alis  类别名
     * @param string $objetName
     * @return mixed    类名
     */
    public function set($alis = '', $objetName = '')
    {
        $this->classMap[$alis] = $objetName;
        if (is_callable($objetName)) {
            return $objetName();
        }
        return new $objetName;
    }

    /**
     * 获取一个类的实例
     *
     * @param string $alias
     * @return object
     */
    public function get($alias = '')
    {
        if (array_key_exists($alias, $this->classMap)) {
            if (is_callable($this->classMap[$alias])) {
                return $this->classMap[$alias];
            }
            return new $this->classMap[$alias];
        }
        throw new CoreHttpException(404, 'Class:' . $alias);
    }

    /**
     * 注入一个单例类
     *
     * @param string $alias 类名或别名
     * @param bject||closure||string $object 实例或闭包或类名
     * @return mixed
     * @throws CoreHttpException
     * @return object
     */
    public function setSingle($alias = '', $object = '')
    {
        if (is_callable($alias)) {
            $instance = $alias();
            $className = get_class($instance);
            $this->instanceMap[$className] = $instance;
            return $instance;
        }

        if (is_callable($object)) {
            if (empty($alias)) {
                throw new CoreHttpException(400, "{$alias} is empty");
            }
        }
        if (array_key_exists($alias, $this->instanceMap)) {
            return $this->instanceMap[$alias];
        }

        if (is_object($alias)) {
            $className = get_class($alias);
            if (array_key_exists($className, $this->instanceMap)) {
                return $this->instanceMap[$alias];
            }
            $this->instanceMap[$className] = $alias;
            return $this->instanceMap[$className];
        }

        if (is_object($object)) {
            if (empty($alias)) {
                throw new CoreHttpException(400, "{$alias} is empty");
            }
            $this->instanceMap[$alias] = $object;
            return $this->instanceMap[$alias];
        }
        if (empty($alias) && empty($object)) {
            throw new CoreHttpException(400, "{$alias} and {$object} is empty");
        }
        $this->instanceMap[$alias] = new $alias();

        return $this->instanceMap[$alias];
    }

    /**
     * 获取一个单例类
     *
     * get a sington instance
     *
     * @param string $alias 类名和别名
     * @param string $closure   闭包
     * @return mixed
     * @throws CoreHttpException
     * @return object
     */
    public function getSingle($alias = '', $closure = '')
    {
        if (array_key_exists($alias, $this->instanceMap)) {
            $instance = $this->instanceMap[$alias];
            if (is_callable($instance)) {
                return $instance();
            }
            return $instance;
        }

        if (is_callable($closure)) {
            return $this->instanceMap[$alias] = $closure();
        }
        throw new CoreHttpException(404, 'Class:' . $alias);
    }
}
