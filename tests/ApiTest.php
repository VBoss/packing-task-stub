<?php declare(strict_types=1);

use App\Application;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;


/** @var EntityManager $entityManager */
$entityManager = require __DIR__ . '/../src/bootstrap.php';

$inputData = [
    [
        \App\Config\InputApi::WIDTH_KEY  => random_int(1, 3),
        \App\Config\InputApi::HEIGHT_KEY  => random_int(1, 3),
        \App\Config\InputApi::LENGTH_KEY  => random_int(1, 3),
        \App\Config\InputApi::WEIGHT_KEY  => random_int(1, 3),
    ],
    [
        \App\Config\InputApi::WIDTH_KEY  => random_int(1, 3),
        \App\Config\InputApi::HEIGHT_KEY  => random_int(1, 3),
        \App\Config\InputApi::LENGTH_KEY  => random_int(1, 3),
        \App\Config\InputApi::WEIGHT_KEY  => random_int(1, 3),
    ],
];
$inputData = \Nette\Utils\Json::encode($inputData);


$request = new Request(
    'POST',
    new Uri('http://localhost/pack'),
    [
        'Content-Type' => 'application/json',
    ],
    $inputData
);

$application = new Application(
    new \App\Request\Decode(
        new \App\Request\Decode\InputProductsFactory(
            new \App\Factory\ProductFactory($entityManager)
        )
    ),
    new \App\Response\StoredResponses(
        $entityManager
    ),
    new \App\Request\PerformRequest(
        $entityManager,
        new \GuzzleHttp\Client()
    )
);

$response = $application->run($request);

echo "<<< In:\n" . Message::toString($request) . "\n\n";
echo ">>> Out:\n" . Message::toString($response) . "\n\n";

