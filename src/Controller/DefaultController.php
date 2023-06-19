<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route(path: '/{vueRouting}', name: 'app_default_main', requirements: ['vueRouting' => '^(?!(api|login)|_(profiler|wdt)).*'], defaults: ['vueRouting' => ''], methods: ['GET'])]
    public function index(Request $request, string $vueRouting): Response
    {
        return $this->render('main.html.twig');
    }
}