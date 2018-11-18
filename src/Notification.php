<?php

namespace Acgn\Center;

use Acgn\Center\Models\User;

class Notification
{
    public const TOPIC_USER_CREATION = 'UserCreation';

    public const TOPIC_USER_CREATION_TESTING = 'UserCreationTesting';

    public const TOPIC_USER_UPDATING = 'UserUpdating';

    public const TOPIC_USER_UPDATING_TESTING = 'UserUpdatingTesting';

    /**
     * @var string
     */
    protected $topicOwner;

    /**
     * @var string
     */
    protected $subscriptionName;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var array
     */
    protected $userCreationListeners = [];

    /**
     * @var array
     */
    protected $userUpdatingListeners = [];

    /**
     * @param string $topicOwner
     * @param string $subscriptionName
     * @param bool $debug
     */
    public function __construct(string $topicOwner, string $subscriptionName, bool $debug = false)
    {
        $this->topicOwner = $topicOwner;
        $this->subscriptionName = $subscriptionName;
        $this->debug = $debug;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function addUserCreationListener(\Closure $callback)
    {
        $this->userCreationListeners[] = $callback;
        return $this;
    }

    /**
     * @param \Closure $callback
     * @return $this
     */
    public function addUserUpdatingListener(\Closure $callback)
    {
        $this->userUpdatingListeners[] = $callback;
        return $this;
    }

    /**
     * @param string $topicOwner
     * @param string $subscriptionName
     * @param string $topicName
     * @param string $message
     */
    protected function dispatch(string $topicOwner, string $subscriptionName, string $topicName, string $message): void
    {
        $message = json_decode($message);

        if ($this->topicOwner != $topicOwner or $this->subscriptionName != $subscriptionName) {
            return;
        }

        if ($this->debug) {
            if ($topicName == self::TOPIC_USER_CREATION_TESTING) {
                $listeners = $this->userCreationListeners;
            } elseif ($topicName == self::TOPIC_USER_UPDATING_TESTING) {
                $listeners = $this->userUpdatingListeners;
            } else {
                return;
            }
        } else {
            if ($topicName == self::TOPIC_USER_CREATION) {
                $listeners = $this->userCreationListeners;
            } elseif ($topicName == self::TOPIC_USER_UPDATING) {
                $listeners = $this->userUpdatingListeners;
            } else {
                return;
            }
        }

        $user = new User($message);

        foreach ($listeners as $callback) {
            call_user_func($callback, $user);
        }
    }

    /**
     * @return void
     */
    public function listen(): void
    {
        // 1. get the headers and check the signature
        $tmpHeaders = [];
        $headers = $this->getHeaders();
        foreach ($headers as $key => $value) {
            if (0 === strpos($key, 'x-mns-')) {
                $tmpHeaders[$key] = $value;
            }
        }
        ksort($tmpHeaders);
        $canonicalizedMNSHeaders = implode(
            "\n",
            array_map(function ($v, $k) {
                return $k . ':' . $v;
            }, $tmpHeaders, array_keys($tmpHeaders))
        );

        $method = $_SERVER['REQUEST_METHOD'];
        $canonicalizedResource = $_SERVER['REQUEST_URI'];

        $contentMd5 = '';
        if (array_key_exists('content-md5', $headers)) {
            $contentMd5 = $headers['content-md5'];
        }

        $contentType = '';
        if (array_key_exists('content-type', $headers)) {
            $contentType = $headers['content-type'];
        }
        $date = $headers['date'];

        $stringToSign = strtoupper($method) . "\n" . $contentMd5 . "\n" . $contentType . "\n" . $date . "\n" . $canonicalizedMNSHeaders . "\n" . $canonicalizedResource;

        $publicKeyURL = base64_decode($headers['x-mns-signing-cert-url']);
        $publicKey = $this->getByUrl($publicKeyURL);
        $signature = $headers['authorization'];

        $pass = $this->verify($stringToSign, $signature, $publicKey);
        if (! $pass) {
            http_response_code(400);
            return;
        }

        // 2. now parse the content
        $content = file_get_contents('php://input');

        if (! empty($contentMd5) and $contentMd5 != base64_encode(md5($content))) {
            http_response_code(401);
            return;
        }

        $content = json_decode($content);
        $this->dispatch($content->TopicOwner, $content->SubscriptionName, $content->TopicName, $content->Message);

        http_response_code(200);
        exit;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function getByUrl(string $url): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }

    /**
     * @param string $data
     * @param string $signature
     * @param string $pubKey
     * @return bool
     */
    protected function verify(string $data, string $signature, string $pubKey): bool
    {
        $res = openssl_get_publickey($pubKey);
        $result = (bool) openssl_verify($data, base64_decode($signature), $res);
        openssl_free_key($res);

        return $result;
    }

    /**
     * @return array
     */
    protected function getHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            $name = strtolower($name);
            if (substr($name, 0, 5) == 'http_') {
                $headers[str_replace('_', '-', substr($name, 5))] = $value;
            }
        }
        return $headers;
    }
}