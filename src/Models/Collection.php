<?php

namespace Acgn\Center\Models;

class Collection extends \ArrayObject
{
    public function __construct(array $input)
    {
        parent::__construct($input);
    }

    public function __get($index)
    {
        if ($this->offsetExists($index)) {
            return $this->offsetGet($index);
        } else {
            throw new \UnexpectedValueException('Undefined key ' . $index);
        }
    }

    public function __set($index, $value)
    {
        $this->offsetSet($index, $value);
        return $this;
    }

    public function __isset($index)
    {
        return $this->offsetExists($index);
    }

    public function __unset($index)
    {
        $this->offsetUnset($index);
    }

    public function __toString()
    {
        return serialize($this);
    }

    public function toArray()
    {
        return array_map(function (Model $model) {
            return $model->toArray();
        }, (array)$this);
    }
}