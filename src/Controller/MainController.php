<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
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
    public function stations(Connection $connection)
    {
        $stations = $connection->fetchAllAssociative("SELECT * FROM stations");

        return new JsonResponse([
            'stations' => $stations,
        ]);
    }

}
