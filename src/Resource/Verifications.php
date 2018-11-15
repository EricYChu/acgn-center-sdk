<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\Request;
use Acgn\Center\Models;

/**
 * @method VerificationsToken token()
 */
class Verifications extends Resource
{
    protected $path = 'verifications';

    /**
     * @param string $captchaResponse
     * @param int $countryCode
     * @param string $phoneNumber
     * @param string $scene
     * @param bool $requireAuthorization
     * @return Models\Verification
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function create(string $captchaResponse, int $countryCode, string $phoneNumber, string $scene, bool $requireAuthorization = false)
    {
        $request = new Request(Request::POST, $this->getPath(), $requireAuthorization);
        $request->setBody([
            'captcha_response' => $captchaResponse,
            'country_code' => $countryCode,
            'phone_number' => $phoneNumber,
            'scene' => $scene,
        ]);

        return $this->client->sendRequest($request, Models\Verification::class);
    }
}
