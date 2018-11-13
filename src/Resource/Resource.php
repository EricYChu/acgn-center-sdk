<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\HttpClient;

abstract class Resource
{
    protected $client;

    protected $parent;

    protected $id;

    protected $path;

    public function __construct(HttpClient $client, $idOrParent = null)
    {
        $this->client = $client;
        if ($idOrParent instanceof Resource) {
            $this->parent = $idOrParent;
        } else {
            $this->id = $idOrParent;
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPath()
    {
        return (empty($this->parent) ? '' : $this->parent->getPath()).'/'.trim($this->path, '/').(empty($this->id) ? '' : '/'.$this->id);
    }

    public function __call($name, $arguments)
    {
        $class = get_class($this).ucfirst($name);
        $instance = new $class($this->client, $this);
        return $instance;
    }
}