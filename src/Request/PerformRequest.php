<?php

namespace App\Request;

use App\DTO\InputProducts;
use App\Entity\Packaging;
use App\Entity\StoredResponse;
use App\Exception\PackingApiException;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Nette\Utils\Json;


class PerformRequest
{

    private EntityManager $entityManager;

    private Client $client;


    public function __construct(
        EntityManager $entityManager,
        Client $client,
    )
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }


    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Nette\Utils\JsonException
     * @throws \Doctrine\ORM\ORMException
     * @throws \App\Exception\PackingApiException
     */
    public function execute(InputProducts $inputProducts): StoredResponse
    {
        $body['username'] = \App\Config\PackingApi::USERNAME;
        $body['api_key'] = \App\Config\PackingApi::KEY;
        $body['items'] = $inputProducts->formatForApi();
        // Expecting doctrine cache is configured, so no local handling with redis or something similar.
        $body['bins'] = $this->entityManager->getRepository(Packaging::class)->findAll();

        $response = $this->client->post(
            \App\Config\PackingApi::URL,
            [
                \GuzzleHttp\RequestOptions::BODY => Json::encode($body),
                \GuzzleHttp\RequestOptions::TIMEOUT => 2,
            ]
        );
        $responseBody = $response->getBody()->getContents();
        $decodedResponseBody = Json::decode($responseBody, \Nette\Utils\Json::FORCE_ARRAY);

        if (
            isset($decodedResponseBody['response']['bins_packed']) === FALSE
            || count($decodedResponseBody['response']['bins_packed']) === 0
        ) {
            $exceptionMessage = 'No bins packed.';
            if (
                isset($decodedResponseBody['response']['errors']) === TRUE
                && count($decodedResponseBody['response']['errors']) > 0
            ) {
                $exceptionMessage .= implode(', ', $decodedResponseBody['response']['errors']);
            }

            throw new PackingApiException($exceptionMessage);
        }

        if (
            isset($decodedResponseBody['response']['errors']) === TRUE
            && count($decodedResponseBody['response']['errors']) > 0
        ) {
            $exceptionMessage = implode(', ', $decodedResponseBody['response']['errors']);

            throw new PackingApiException($exceptionMessage);
        }

        $storedResponse = new StoredResponse(
            $inputProducts->getKey(),
            reset($decodedResponseBody['response']['bins_packed']),
        );

        $this->entityManager->persist($storedResponse);
        $this->entityManager->flush();

        return $storedResponse;
    }

}
