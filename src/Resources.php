<?php

namespace Acgn\Center;

use Acgn\Center\Http\HttpClient;
use Acgn\Center\Resource;

/**
 * @method Resource\Verifications verifications(?string $id = null)
 * @method Resource\Users users(?string $id = null)
 * @method Resource\Forgot forgot()
 * @method Resource\Auth auth()
 */
class Resources
{
    protected $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function __call($name, $arguments)
    {
        $class = __NAMESPACE__.'\Resource\\'.ucfirst($name);
        if (empty($arguments)) {
            $instance = new $class($this->client);
        } else {
            $instance = new $class($this->client, $arguments[0]);
        }
        return $instance;
    }
}