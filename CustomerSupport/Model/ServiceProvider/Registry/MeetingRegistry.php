<?php

namespace AmeshExtensions\CustomerSupport\Model\ServiceProvider\Registry;

class MeetingRegistry
{
    private $registry = [];

    public function register(string $namespace, $value)
    {
        $this->registry[$namespace] = $value;
    }

    public function registry($namespace)
    {
        if (isset($this->registry[$namespace])) {
            return $this->registry[$namespace];
        }
        return null;
    }

    public function clear($namespace)
    {
        if (isset($this->registry[$namespace])) {
            unset($this->registry[$namespace]);
        }
    }
}
