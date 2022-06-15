<?php

namespace App\Request\Decode;

use App\DTO\InputProducts;
use App\Exception\EmptyInputException;
use App\Exception\OmitProductException;
use App\Factory\ProductFactory;


class InputProductsFactory
{

    private ProductFactory $productFactory;


    public function __construct(
        ProductFactory $productFactory
    )
    {
        $this->productFactory = $productFactory;
    }


    /**
     * @throws \App\Exception\EmptyInputException
     * @throws \App\Exception\OmitProductException
     * @throws \Doctrine\ORM\ORMException
     */
    public function create(array $decodedJsonProducts): InputProducts
    {
        $products = new InputProducts();
        foreach ($decodedJsonProducts as $jsonProduct) {
            try {
                $product = $this->productFactory->create($jsonProduct);

            } catch (OmitProductException $omitProductException) {
                // If we want to accept incomplete product data, no exception is thrown and product is skipped,
                // order is processed and problem must be resolved later maybe in warehouse?
                throw $omitProductException;
            }

            $products->add($product);
        }

        if ($products->count() === 0) {
            throw new EmptyInputException('No valid products received.');
        }

        return $products;
    }

}
