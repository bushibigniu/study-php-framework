<?php

namespace Framework;

use Framework\App;
use Framework\Exceptions\CoreHttpException;

/**
 * 注册加载handle
 *
 */
class Load
{
    /**
     * 类名映射
     *
     * @var array
     */
    public static $map = [];

    /**
     * 类命名空间映射
     *
     * @var array
     */
    public static $namespaceMap = [];

    /**
     * 应用启动注册.
     *
     * @param  App $app 框架实例
     * @return mixed
     */
    public static function register(App $app)
    {
        self::$namespaceMap = [
            'Framework'=>$app->rootPath
        ];

        // 注册框架加载函数　不使用composer加载机制加载框架　自己实现
        spl_autoload_register(['Framework\Load','autoload']);

        require($app->rootPath . '/vendor/autoload.php');
    }

    private static function autoload($class)
    {
        $classOrigin = $class;
        $classInfo = explode('\\', $class);
        $className = array_pop($classInfo);

        foreach ($classInfo as &$v) {
            $v = strtolower($v);
        }
        unset($v);
        array_push($classInfo, $className);
        $class = explode('\\', $classInfo);
        $path = self::$namespaceMap['Framework'];
        $classPath = $path . '/'.str_replace('\\', '/', $class) . '.php';

        if (!file_exists($classPath)) {
            // 框架级别加载文件不存在　composer加载
            return;
            throw new CoreHttpException(404, "$classPath Not Found");
        }

        self::$namespaceMap[$classOrigin] = $classPath;

        return $classPath;
    }
}
