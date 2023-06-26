<?php

namespace App\Controller;

use App\Manager\LinkManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/click')]
class ClickController extends AbstractController
{
    #[Route('/{identifier}', name: 'link.click', methods: ['GET'])]
    public function index(LinkManager $manager, string $identifier): Response
    {
        $linkId = $manager->click($identifier);

        return $this->render('link/click.html.twig', [
            'link_id' => $linkId,
        ]);
    }
}