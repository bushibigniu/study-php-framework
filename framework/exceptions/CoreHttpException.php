<?php

namespace Framework\Exceptions;

use Exception;
use Framework\App;

class CoreHttpException extends Exception
{
    /**
     * 响应异常code
     *
     * @var array
     */
    private $httpCode = [
        400 =>'Bad Request',
        403 =>'Forbidden',
        404 =>'Not Found',
        500 =>'Internet Server Error',
        // Remote Service error
        503 =>'Service Unavailable',
    ];

    /**
     * CoreHttpException constructor.
     * @param int $code
     * @param string $extra 错误信息补充
     */
    public function __construct($code = 200, $extra = '')
    {
        $this->code = $code;
        if(empty($extra)){
            $this->message = $this->httpCode[$code];
            return;
        }
        $this->message = $extra . ' ' . $this->httpCode[$code];
    }

    /**
     * rest 风格http响应
     *
     * @return json
     */
    public function reponse()
    {
        $data = [
            '__coreError' => [
                'code'    =>$this->getCode(),
                'message' =>$this->getMessage(),
                'informations'  =>[
                    'file'  => $this->getFile(),
                    'line'  => $this->getLine(),
                    'trace' => $this->getTrace(),
                ],
            ]
        ];

        //log
        App::$container->getSingle('logger')->write($data);
        //response
        header('Content-Type:Application/json; Charset=utf-8');
        die(json_decode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * rest 风格http异常响应
     *
     * @param $e    异常
     * @return json
     */
    public static function reponseErr($e)
    {
        $data = [
            '__coreError' => [
                'code' => 500,
                'message'   =>$e,
                'informations'=>[
                    'file'=>$e['file'],
                    'line'=>$e['line'],
                ],
            ],
        ];

        //log
        App::$container->getSingle('logger')->write($data);
        header('Content-Type:Application/json; Charset=utf-8');
        die(json_encode($data));
    }

}