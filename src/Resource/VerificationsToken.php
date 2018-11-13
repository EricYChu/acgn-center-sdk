<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\Request;
use Acgn\Center\Models;

class VerificationsToken extends Resource
{
    protected $path = 'token';

    /**
     * @param string $verificationCode
     * @return Models\VerificationToken
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function get(string $verificationCode)
    {
        $request = new Request(Request::GET, $this->getPath(), false);
        $request->setQueryString([
            'verification_code' => $verificationCode,
        ]);

        return $this->client->sendRequest($request, Models\VerificationToken::class);
    }
}
