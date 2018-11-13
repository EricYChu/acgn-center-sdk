<?php

namespace Acgn\Center;

use Acgn\Center\Http\HttpClient;

class Client
{
    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var Resources
     */
    protected $resources;

    /**
     * Client constructor.
     * @param string $appKey
     * @param string $appSecret
     * @param string $endpoint
     * @param null|string $language
     * @param null|string $accessToken
     */
    public function __construct(string $appKey, string $appSecret, string $endpoint, ?string $language = null, ?string $accessToken = null)
    {
        $this->client = new HttpClient($appKey, $appSecret, $endpoint, $language, $accessToken);
        $this->resources = new Resources($this->client);
    }

    /**
     * @return $this
     */
    public function getAccessToken()
    {
        $this->client->getAccessToken();
        return $this;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function setAccessToken(string $token)
    {
        $this->client->setAccessToken($token);
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->client->getLanguage();
    }

    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language)
    {
        $this->client->setLanguage($language);
        return $this;
    }

    /**
     * @return Resources
     */
    public function resources(): Resources
    {
        return $this->resources;
    }
}