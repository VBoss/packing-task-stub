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
    private string $key;

    /**
     * @ORM\Column(type="json")
     */
    private $response;


    public function __construct(
        string $key,
        array $response,
    )
    {
        $this->key = $key;
        $this->response = $response;
    }


    public function getKey(): string
    {
        return $this->key;
    }


    public function getResponse(): array
    {
        return $this->response;
    }

}
