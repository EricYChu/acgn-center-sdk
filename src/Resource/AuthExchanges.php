<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\Request;
use Acgn\Center\Models;

/**
 */
class AuthExchanges extends Resource
{
    protected $path = 'exchanges';

    /**
     * @param string $token
     * @return Models\IntermediateAuth
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function create(string $token)
    {
        $request = new Request(Request::POST, $this->getPath(), false);
        $request->setBody([
            'token' => $token,
        ]);

        return $this->client->sendRequest($request, Models\Auth::class);
    }
}
