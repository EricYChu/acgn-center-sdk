<?php

namespace Acgn\Center\Resource;

use Acgn\Center\Http\Request;
use Acgn\Center\Models;

/**
 */
class AuthIntermediates extends Resource
{
    protected $path = 'intermediates';

    /**
     * @return Models\IntermediateAuth
     * @throws \Acgn\Center\Exceptions\HttpTransferException
     * @throws \Acgn\Center\Exceptions\ParseResponseException
     * @throws \Acgn\Center\Exceptions\ResponseException
     */
    public function create()
    {
        $request = new Request(Request::POST, $this->getPath(), true);

        return $this->client->sendRequest($request, Models\IntermediateAuth::class);
    }
}
