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
     * @param int $countryCode
     * @param string $phoneNumber
     * @return null
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function update(string $verificationToken, int $countryCode, string $phoneNumber)
    {
        $request = new Request(Request::PUT, $this->getPath(), true);
        $request->setBody([
            'verification_token' => $verificationToken,
            'country_code' => $countryCode,
            'phone_number' => $phoneNumber,
        ]);

        return $this->client->sendRequest($request);
    }
}
