<?php

namespace Acgn\Center\Http;


use Acgn\Center\Config;
use Acgn\Center\Exceptions\HttpTransferException;
use Acgn\Center\Exceptions\InvalidParamsResponseException;
use Acgn\Center\Models\Collection;
use Acgn\Center\Models\ModelInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;

class HttpClient
{
    private $appKey;
    private $appSecret;
    private $endpoint;
    private $language;
    private $client;
    private $accessToken;
    private $requestTimeout;
    private $connectTimeout;

    /**
     * HttpClient constructor.
     * @param string $appKey
     * @param string $appSecret
     * @param string $endpoint
     * @param string $language
     * @param null|string $accessToken
     * @param Config|null $config
     */
    public function __construct(string $appKey, string $appSecret, string $endpoint, string $language, ?string $accessToken = null, ?Config $config = null)
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->endpoint = $endpoint;
        $this->language = $language;

        if (empty($config)) {
            $config = new Config;
        }

        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $endpoint,
            'defaults' => [
                'headers' => [
                    'Host' => $endpoint
                ],
                'proxy' => $config->getProxy(),
                'expect' => $config->getExpectContinue()
            ]
        ]);
        $this->requestTimeout = $config->getRequestTimeout();
        $this->connectTimeout = $config->getConnectTimeout();
        $this->accessToken = $accessToken;
        $this->endpoint = $endpoint;
    }

    /**
     * @return null|string
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @param null|string $accessToken
     * @return $this
     */
    public function setAccessToken(?string $accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language)
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @param Request $request
     */
    private function addRequiredHeaders(Request $request)
    {
        $request->setHeader('Accept', 'application/vnd.acgn.v1+json');
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('Accept-Language', $this->language);
        $request->setHeader('X-App-Key', $this->appKey);

        if (! empty($this->accessToken)) {
            $request->setHeader('Authorization', 'Bearer '.$this->accessToken);
        }
    }

    /**
     * @param Request $request
     * @param string|null $model
     * @return Collection|ModelInterface|null
     * @throws InvalidParamsResponseException
     * @throws HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function sendRequest(Request $request, ?string $model = null)
    {
        $promise = $this->sendRequestAsync($request, $model);
        return $promise->wait();
    }

    /**
     * @param Request $request
     * @param string|null $model
     * @return Promise
     * @throws HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function sendRequestAsync(Request $request, ?string $model = null)
    {
        $response = new Response($model);
        $promise = $this->sendRequestAsyncInternal($request, $response);
        return new Promise($promise, $response);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \GuzzleHttp\Promise\PromiseInterface
     * @throws HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    private function sendRequestAsyncInternal(Request $request, Response $response)
    {
        $this->addRequiredHeaders($request);

        $parameters = [
            'exceptions' => false,
            'http_errors' => false,
        ];

        $queryString = $request->getQueryString();
        if (! empty($queryString)) {
            $parameters['query'] = $queryString;
        }

        $body = $request->serializeBody();
        if (! empty($body)) {
            $parameters['body'] = $body;
        }

        $parameters['timeout'] = $this->requestTimeout;
        $parameters['connect_timeout'] = $this->connectTimeout;

        $request = new \GuzzleHttp\Psr7\Request(
            $request->getMethod(),
            $request->getResourcePath(),
            $request->getHeaders()
        );

        try {
            return $this->client->sendAsync($request, $parameters);
        } catch (RequestException $e) {
            $res = $e->getResponse();
            $response->parseErrorResponse($res->getStatusCode(), $res->getBody());
        } catch (TransferException $e) {
            $message = $e->getMessage();
            throw new HttpTransferException($message, $e->getCode());
        }
    }
}