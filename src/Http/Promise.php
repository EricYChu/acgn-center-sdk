<?php

namespace Acgn\Center\Http;

use Acgn\Center\Models\Collection;
use Acgn\Center\Models\ModelInterface;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Exception\TransferException;
use Psr\Http\Message\ResponseInterface;

class Promise
{
    private $response;
    private $promise;

    public function __construct(PromiseInterface $promise, Response $response)
    {
        $this->promise = $promise;
        $this->response = $response;
    }

    public function isCompleted()
    {
        return $this->promise->getState() != 'pending';
    }

    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Collection|ModelInterface|null
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function wait()
    {
        try {
            $res = $this->promise->wait();
            if ($res instanceof ResponseInterface) {
                $this->response->parseResponse($res->getStatusCode(), $res->getBody()->getContents());
            }
        } catch (TransferException $e) {
            $message = $e->getMessage();
            if ($e->hasResponse()) {
                $message = $e->getResponse()->getBody();
            }
            $this->response->parseErrorResponse($e->getCode(), $message);
        }

        $model = $this->response->getModel();
        if ($model) {
            $body = $this->response->getBody();
            if (is_array($body->data)) {
                $collection = [];
                foreach ($body->data as $datum) {
                    $collection[] = new $model($datum);
                }
                return new Collection($collection);
            }
            return new $model($this->response->getBody());
        }

        return null;
    }
}
