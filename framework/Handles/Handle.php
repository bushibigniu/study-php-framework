<?php

namespace Framework\Handles;

use Framework\App;

Interface Handle
{
    /**
     * 注册处理机制
     *
     * @param App $app
     * @return mixed
     */
    public function register(App $app);
}