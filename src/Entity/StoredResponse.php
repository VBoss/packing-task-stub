<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class StoredResponse
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $requestKey;

    /**
     * @ORM\Column(type="json")
     */
    private array $response;


    public function __construct(
        string $requestKey,
        array  $response,
    )
    {
        $this->requestKey = $requestKey;
        $this->response = $response;
    }


    public function getRequestKey(): string
    {
        return $this->requestKey;
    }


    public function getResponse(): array
    {
        return $this->response;
    }

}
