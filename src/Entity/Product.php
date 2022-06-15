<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Product
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="float")
     */
    private float $width;

    /**
     * @ORM\Column(type="float")
     */
    private float $height;

    /**
     * @ORM\Column(type="float")
     */
    private float $length;

    /**
     * @ORM\Column(type="float")
     */
    private float $weight;

    /**
     * @ORM\Column(type="float")
     */
    private float $quantity;


    public function __construct(
        float $width,
        float $height,
        float $length,
        float $weight,
        float $quantity
    ) {
        $this->width = $width;
        $this->height = $height;
        $this->length = $length;
        $this->weight = $weight;
        $this->quantity = $quantity;
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getWidth(): float
    {
        return $this->width;
    }


    public function getHeight(): float
    {
        return $this->height;
    }


    public function getLength(): float
    {
        return $this->length;
    }


    public function getWeight(): float
    {
        return $this->weight;
    }


    public function getQuantity(): float
    {
        return $this->quantity;
    }


    public function addQuantity($amount): void
    {
        $this->quantity += $amount;
    }

}
