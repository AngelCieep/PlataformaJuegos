<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

class ErrorTestController extends AbstractController
{
    #[Route('/test/error/404', name: 'app_test_error_404')]
    public function test404(): Response
    {
        throw new NotFoundHttpException('Página no encontrada - Test 404');
    }

    #[Route('/test/error/403', name: 'app_test_error_403')]
    public function test403(): Response
    {
        throw new AccessDeniedHttpException('Acceso denegado - Test 403');
    }

    #[Route('/test/error/500', name: 'app_test_error_500')]
    public function test500(): Response
    {
        throw new \Exception('Error interno del servidor - Test 500');
    }
}
