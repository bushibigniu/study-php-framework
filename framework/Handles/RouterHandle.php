<?php

namespace Framework\Handles;

use Framework\App;

class RouterHandle implements Handle
{
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
}