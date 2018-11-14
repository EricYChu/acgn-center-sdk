<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\Request;

/**
 */
class UsersPhone extends Resource
{
    protected $path = 'phone';

    /**
     * @param string $verificationToken
     * @return null
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function update(string $verificationToken)
    {
        $request = new Request(Request::PUT, $this->getPath(), true);
        $request->setBody([
            'verification_token' => $verificationToken,
        ]);

        return $this->client->sendRequest($request);
    }
}
