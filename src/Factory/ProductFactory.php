<?php

namespace App\Factory;

use App\Config\PackingApi;
use App\Entity\Product;
use App\Exception\OmitProductException;
use Doctrine\ORM\EntityManager;


class ProductFactory
{

    private EntityManager $entityManager;


    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param array<string, float> $item
     * @throws \Doctrine\ORM\ORMException
     * @throws \App\Exception\OmitProductException
     */
    public function create(array $item): Product
    {
        \set_error_handler([$this, 'errorHandler'], \E_NOTICE);

        $product = $this->entityManager
            ->getRepository(Product::class)
            ->findOneBy([
                'width' => $item[\App\Config\InputApi::WIDTH_KEY],
                'height' => $item[\App\Config\InputApi::HEIGHT_KEY],
                'length' => $item[\App\Config\InputApi::LENGTH_KEY],
                'weight' => $item[\App\Config\InputApi::WEIGHT_KEY],
            ])
        ;

        if ($product instanceof Product) {
            return $product;
        }

        $product = new Product(
            $item[\App\Config\InputApi::WIDTH_KEY],
            $item[\App\Config\InputApi::HEIGHT_KEY],
            $item[\App\Config\InputApi::LENGTH_KEY],
            $item[\App\Config\InputApi::WEIGHT_KEY],
            1,
        );

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        \restore_error_handler();

        return $product;
    }


    /**
     * @throws \App\Exception\OmitProductException
     */
    protected function errorHandler(
        int     $errno,
        string  $errorString,
        ?string $errorFile,
        ?int    $errorLine,
        ?array  $errcontext
    ): bool
    {
        if (\strpos($errorString, 'Undefined index') !== FALSE) {
            if ($errorFile) {
                $errorString .= \sprintf("; File: '%s'.", $errorFile);
            }
            if ($errorLine) {
                $errorString .= \sprintf("; Line: '%d'.", $errorLine);
            }

            throw new OmitProductException($errorString);
        }

        return FALSE;
    }

}
