<?php

namespace Acgn\Center\Models;

use Carbon\Carbon;
use DateTimeInterface;

class Model extends \ArrayObject implements ModelInterface
{
    protected $casts = [];

    public function __construct(\stdClass $input)
    {
        if (! empty($input->data)) {
            $input = $input->data;
        }

        foreach ($this->casts as $key => $type) {
            if (property_exists($input, $key)) {
                $input->{$key} = $this->cast($type, $input->{$key});
            }
        }
        parent::__construct((array)$input);
    }

    protected function cast(string $type, $value)
    {
        switch ($type) {
            case 'int':
            case 'integer':
                return (int) $value;

                case 'real':
            case 'float':
            case 'double':
                return (float) $value;
            case 'string':
                return (string) $value;

            case 'bool':
            case 'boolean':
                return (bool) $value;

            case 'object':
                return $this->fromJson($value, true);

            case 'array':
            case 'json':
                return $this->fromJson($value);

            case 'date':
                return $this->asDate($value);

            case 'datetime':
            case 'custom_datetime':
                return $this->asDateTime($value);

            case 'timestamp':
                return $this->asTimestamp($value);
        }

        if (stripos($type, __NAMESPACE__) >= 0) {
            return new $type($value);
        }

        return $value;
    }

    /**
     * @param  string  $value
     * @param  bool  $asObject
     * @return mixed
     */
    public function fromJson($value, $asObject = false)
    {
        return json_decode($value, ! $asObject);
    }

    /**
     * @param  mixed  $value
     * @return int
     */
    protected function asTimestamp($value)
    {
        return $this->asDateTime($value)->getTimestamp();
    }

    /**
     * @param  mixed  $value
     * @return Carbon
     */
    protected function asDate($value)
    {
        return $this->asDateTime($value)->startOfDay();
    }

    /**
     * @param  mixed  $value
     * @return Carbon
     */
    protected function asDateTime($value)
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof DateTimeInterface) {
            return new Carbon(
                $value->format('Y-m-d H:i:s.u'), $value->getTimezone()
            );
        }

        if (is_numeric($value)) {
            return Carbon::createFromTimestamp($value);
        }

        if ($this->isStandardDateFormat($value)) {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        return Carbon::createFromFormat(DateTimeInterface::RFC3339, $value);
    }

    /**
     * @param  string  $value
     * @return bool
     */
    protected function isStandardDateFormat($value)
    {
        return preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value);
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
        $output = [];
        foreach ($this as $key => $value) {
            if ($value instanceof Carbon) {
                $value = $value->toRfc3339String();
            } elseif ($value instanceof Model) {
                $value = $value->toArray();
            }
            $output[$key] = $value;
        }
        return $output;
    }
}