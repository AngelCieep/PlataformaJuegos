<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApiDocController extends AbstractController
{
    #[Route('/api/doc', name: 'app_api_doc')]
    public function index(): Response
    {
        return $this->render('api_doc/index.html.twig', [
            'controller_name' => 'ApiDocController',
        ]);
    }
}
