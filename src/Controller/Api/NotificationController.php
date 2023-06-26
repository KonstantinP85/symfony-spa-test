<?php

namespace App\Controller\Api;

use App\Manager\NotificationManager;
use App\Service\DtoConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/api/v1/notification/email')]
class NotificationController extends AbstractController
{
    #[Route('/send', name: 'api.v1.notification.email', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the list.')]
    public function sendEmail(Request $request, DtoConverter $converter, NotificationManager $manager)
    {
        try {
            $manager->sendEmail();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}