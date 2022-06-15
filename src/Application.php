<?php

namespace App;

use App\Exception\ApplicationException;
use App\Exception\NoStoredResponseException;
use App\Request\PerformRequest;
use App\Response\StoredResponses;
use GuzzleHttp\Psr7\Response;
use Nette\Utils\Json;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Application
{
    private const MAX_ATTEMPTS = 2;

    private int $attempts = 0;

    private \App\Request\Decode $decode;

    private StoredResponses $storedResponses;

    private PerformRequest $performRequest;


    public function __construct(
        \App\Request\Decode $decode,
        StoredResponses $storedResponses,
        PerformRequest $performRequest
    )
    {
        $this->decode = $decode;
        $this->storedResponses = $storedResponses;
        $this->performRequest = $performRequest;
    }


    public function run(RequestInterface $request): ResponseInterface
    {
        try {
            // Extract products
            $inputProducts = $this->decode->execute($request);

            // Check if result is cached
            try {
                $storedResponse = $this->storedResponses->find($inputProducts);

                return new Response(200, [], Json::encode($storedResponse->getResponse()));

            } catch (NoStoredResponseException $noStoredResponseException) {
                // Can be logged if required. ie: too many misses in cache.
            }

            $response = $this->performRequest->execute($inputProducts);

            return new Response(200, [], Json::encode($response->getResponse()));

        } catch (\GuzzleHttp\Exception\GuzzleException $exception) {
            if ($this->attempts < self::MAX_ATTEMPTS) {
                $this->attempts++;

                return $this->run($request);
            }


        } catch (ApplicationException|\Exception $exception) {
            // Can be logged if required.
        }

        return new Response(
            200,
            [],
            Json::encode([
                'error' => $exception->getMessage()
            ])
        );
    }

}
