<?php

namespace App\Request;

use App\DTO\InputProducts;
use App\Exception\JsonNotValidException;
use App\Request\Decode\InputProductsFactory;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Http\Message\RequestInterface;


class Decode
{

    private InputProductsFactory $inputProductsFactory;


    public function __construct(
        InputProductsFactory $inputProductsFactory
    )
    {
        $this->inputProductsFactory = $inputProductsFactory;
    }


    /**
     * @throws \App\Exception\JsonNotValidException
     * @throws \App\Exception\EmptyInputException
     * @throws \App\Exception\OmitProductException
     * @throws \Doctrine\ORM\ORMException
     */
    public function execute(RequestInterface $request): InputProducts
    {
        try {
            $decodedJsonProducts = Json::decode($request->getBody()->getContents(), true);

        } catch (JsonException $jsonException) {
            throw new JsonNotValidException($jsonException->getMessage(), $jsonException->getCode());
        }

        return $this->inputProductsFactory->create($decodedJsonProducts);
    }

}
