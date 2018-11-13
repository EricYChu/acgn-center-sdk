<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\Request;
use Acgn\Center\Models;

/**
 * @method VerificationsToken verifications()
 * @method UsersPassword password()
 * @method UsersPhone phone()
 */

class Users extends Resource
{
    protected $path = 'users';

    /**
     * @param string $verificationToken
     * @param string $username
     * @param string $password
     * @return Models\User
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function create(string $verificationToken, string $username, string $password)
    {
        $request = new Request(Request::POST, $this->getPath(), false);
        $request->setBody([
            'verification_token' => $verificationToken,
            'username' => $username,
            'password' => $password,
        ]);

        return $this->client->sendRequest($request, Models\User::class);
    }

    /**
     * @return Models\User|Models\Collection
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function get()
    {
        $request = new Request(Request::GET, $this->getPath(), true);

        return $this->client->sendRequest($request, Models\User::class);
    }

    /**
     * @param null|string $username
     * @param null|string $email
     * @return Models\User
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function update(?string $username = null, ?string $email = null)
    {
        $request = new Request(Request::PATCH, $this->getPath(), true);
        $request->setBody([
            'username' => $username,
            'email' => $email,
        ]);

        return $this->client->sendRequest($request, Models\User::class);
    }
}
