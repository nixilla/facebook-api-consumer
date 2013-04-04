<?php

namespace Facebook\Object;

class User implements \ArrayAccess
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

    public function __toString()
    {
        return sprintf('%s %s', $this->getFirstName(), $this->getLastName());
    }

    public function getFirstName()
    {
        return isset($this->container['first_name']) ? $this->container['first_name'] : null;
    }

    public function getLastName()
    {
        return isset($this->container['last_name']) ? $this->container['last_name'] : null;
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