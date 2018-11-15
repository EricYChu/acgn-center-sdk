<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\Request;
use Acgn\Center\Models;

/**
 */
class UsersEmail extends Resource
{
    protected $path = 'email';

    /**
     * @param string $email
     * @return Models\User
     * @throws \Acgn\Center\Exceptions\InvalidParamsResponseException
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function update(string $email)
    {
        $request = new Request(Request::PUT, $this->getPath(), true);
        $request->setBody([
            'email' => $email,
        ]);

        return $this->client->sendRequest($request, Models\User::class);
    }
}
