<?php

namespace Facebook\Object;

class User
{
    private $container = array();

    public function fromArray(array $input)
    {
        $this->container = $input;
    }

    public function toArray()
    {
        return $this->container;
    }

    // ArrayAccess
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) $this->container[] = $value;
        else$this->container[$offset] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}