<?php

namespace Acgn\Center\Http;

class Request
{
    public const POST = 'POST';
    public const GET = 'GET';
    public const PATCH = 'PATCH';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var string
     */
    protected $resourcePath;

    /**
     * @var bool
     */
    protected $requireAuthorization = false;

    /**
     * @var array
     */
    protected $queryString = [];

    /**
     * @var string|array|null
     */
    protected $body;

    /**
     * @param string $method
     * @param string $resourcePath
     * @param bool $requireAuthorization
     */
    public function __construct(string $method, string $resourcePath, bool $requireAuthorization = false)
    {
        $this->method = strtoupper($method);
        $this->resourcePath = $resourcePath;
        $this->requireAuthorization = $requireAuthorization;
    }

    /**
     * @param array $queryString
     */
    public function setQueryString(array $queryString): void
    {
        $this->queryString = $queryString;
    }

    /**
     * @return array
     */
    public function getQueryString(): array
    {
        return $this->queryString;
    }

    /**
     * @param string|array|null $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * @return string|array|null
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string|null
     */
    public function serializeBody()
    {
        if ($this->body === null) {
            return null;
        } elseif (is_array($this->body)) {
            $body = array_filter($this->body);
            return empty($body) ? null : json_encode($body);
        } else {
            return $this->body;
        }
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getResourcePath(): string
    {
        return $this->resourcePath;
    }

    /**
     * @return bool
     */
    public function isRequireAuthorization(): bool
    {
        return $this->requireAuthorization;
    }
}