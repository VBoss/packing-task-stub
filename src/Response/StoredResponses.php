<?php

namespace App\Response;


use App\DTO\InputProducts;
use App\Entity\StoredResponse;
use App\Exception\NoStoredResponseException;
use Doctrine\ORM\EntityManager;


class StoredResponses
{

    private EntityManager $entityManager;


    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @throws \App\Exception\NoStoredResponseException
     */
    public function find(InputProducts $inputProducts): StoredResponse
    {
        $storedResponse = $this->entityManager
            ->getRepository(StoredResponse::class)
            ->findOneBy(
                [
                    'key' => $inputProducts->getKey(),
                ]
            )
        ;

        if ($storedResponse instanceof StoredResponse) {
            return $storedResponse;
        }

        throw new NoStoredResponseException('No stored response found.');
    }

}
