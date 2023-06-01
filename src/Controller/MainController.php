<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MainController
{
    #[Route("/")]
    public function home()
    {
        return new JsonResponse(['message' => 'Hello from backend API']);
    }

    #[Route("/stations.json")]
    public function stations()
    {
        return new JsonResponse([
            'list' => [
                [
                    'id' => 1,
                    'name' => 'Station 1',
                    'lat' => 33.5731104,
                    'lng' => -7.5898434,
                ],
                [
                    'id' => 2,
                    'name' => 'Station 2',
                    'lat' => 33.5731104,
                    'lng' => -7.5898434,
                ]
            ]
        ]);
    }

}
