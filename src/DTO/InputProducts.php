<?php declare(strict_types=1);

namespace App\DTO;

use App\Config\PackingApi;
use App\Entity\Product;


class InputProducts
{

    /**
     * @var array<\App\Entity\Product>
     */
    private array $products;


    public function __construct(
        Product ... $products
    ) {
        foreach ($products as $product) {
            $this->add($product);
        }
    }


    public function getKey(): string
    {
        $keys = [];
        /** @var \App\Entity\Product $product */
        foreach ($this->products as $product) {
            $productQuantityKey = $product->getId() . ':' . $product->getQuantity();
            $keys[$productQuantityKey] = $productQuantityKey;
        }

        \ksort($keys);

        // MD5 just in case there are too many items
        return \md5(\implode(';', $keys));
    }


    public function add(Product $product): void
    {
        if (isset($this->products[$product->getId()])) {
            $this->products[$product->getId()]->addQuantity(1);

        } else {
            $this->products[$product->getId()] = $product;
        }
    }


    public function count(): int
    {
        return \count($this->products);
    }

    public function formatForApi(): array
    {
        $array = [];

        foreach ($this->products as $product) {
            $array[] = [
                PackingApi::ID_KEY => $product->getId(),
                PackingApi::WIDTH_KEY => $product->getWidth(),
                PackingApi::HEIGHT_KEY => $product->getHeight(),
                PackingApi::LENGTH_KEY => $product->getLength(),
                PackingApi::WEIGHT_KEY => $product->getWeight(),
                PackingApi::QUANTITY_KEY => $product->getQuantity(),
                PackingApi::VR_KEY => TRUE,
            ];
        }

        return $array;
    }

}
