<?php

namespace Acgn\Center\Http;

use Acgn\Center\Exceptions\InvalidParamsResponseException;
use Acgn\Center\Exceptions\ParseResponseException;
use Acgn\Center\Exceptions\ResponseException;

class Response
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var mixed
     */
    protected $body;

    /**
     * @var string
     */
    protected $model;

    /**
     * @param string|null $model
     */
    public function __construct(?string $model = null)
    {
        $this->model = $model;
    }

    /**
     * @param $body
     * @return mixed
     * @throws ParseResponseException
     */
    protected function parseJsonBody($body)
    {
        try {
            return json_decode($body);
        } catch (\Throwable $e) {
            throw new ParseResponseException($e->getMessage());
        }
    }

    /**
     * @param int $statusCode
     * @param $body
     * @throws ParseResponseException
     * @throws ResponseException
     */
    public function parseResponse(int $statusCode, $body)
    {
        if ($statusCode < 400) {
            $this->statusCode = $statusCode;
            if (! empty($body)) {
                $this->body = $this->parseJsonBody($body);
                if (! property_exists($this->body, 'data')) {
                    throw new ParseResponseException('No \'data\' found in response body.', $statusCode);
                }
            }
        } else {
            $this->parseErrorResponse($statusCode, $body);
        }
    }

    /**
     * @param int $statusCode
     * @param $body
     * @throws InvalidParamsResponseException
     * @throws ParseResponseException
     * @throws ResponseException
     */
    public function parseErrorResponse(int $statusCode, $body)
    {
        $this->statusCode = $statusCode;
        $this->body = $this->parseJsonBody($body);

        if (! array_key_exists('message', $this->body)) {
            throw new ParseResponseException('No \'message\' found in response body.');
        }

        $message = $this->body->message;

        if ($statusCode === 422) {
            if (! property_exists($this->body, 'errors')) {
                throw new ParseResponseException('No \'errors\' found in response body.');
            }
            throw new InvalidParamsResponseException($message, $statusCode, $this->body->errors);
        }

        throw new ResponseException($message, $statusCode);
    }

    /**
     * @return string
     */
    public function getModel(): ?string
    {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->statusCode < 400;
    }

    /**
     * @return bool
     */
    public function isFailure(): bool
    {
        return $this->statusCode >= 400;
    }
}