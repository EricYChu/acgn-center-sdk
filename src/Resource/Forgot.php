<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\Request;

/**
 */
class Forgot extends Resource
{
    protected $path = 'user/password';

    /**
     * @param string $verificationToken
     * @param string $password
     * @return null
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function create(string $verificationToken, string $password)
    {
        $request = new Request(Request::PUT, $this->getPath(), false);
        $request->setBody([
            'verification_token' => $verificationToken,
            'password' => $password,
        ]);

        return $this->client->sendRequest($request);
    }
}
