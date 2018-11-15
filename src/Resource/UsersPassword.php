<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\Request;
use Acgn\Center\Models;

/**
 */
class UsersPassword extends Resource
{
    protected $path = 'password';

    /**
     * @param string $password
     * @param string $current_password
     * @return null
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function update(string $password, string $current_password)
    {
        $request = new Request(Request::PUT, $this->getPath(), true);
        $request->setBody([
            'password' => $password,
            'current_password' => $current_password,
        ]);

        return $this->client->sendRequest($request, Models\User::class);
    }
}
