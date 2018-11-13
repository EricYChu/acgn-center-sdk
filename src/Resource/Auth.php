<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\Request;
use Acgn\Center\Models;

/**
 * @method AuthRenewal renewal()
 */
class Auth extends Resource
{
    protected $path = 'auth';

    /**
     * @param string $identification
     * @param string $password
     * @return Models\Auth
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function create(string $identification, string $password)
    {
        $request = new Request(Request::POST, $this->getPath(), false);
        $request->setBody([
            'identification' => $identification,
            'password' => $password,
        ]);

        return $this->client->sendRequest($request, Models\Auth::class);
    }

    /**
     * @return null
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function delete()
    {
        $request = new Request(Request::DELETE, $this->getPath(), true);

        return $this->client->sendRequest($request);
    }
}
