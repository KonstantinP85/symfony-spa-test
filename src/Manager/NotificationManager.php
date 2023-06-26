<?php

namespace App\Manager;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class NotificationManager
{
    public function __construct(private readonly MailerInterface $mailer)
    {
    }

    public function sendEmail(): void
    {
        $email = (new TemplatedEmail())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('Time for Symfony Mailer!')
            ->htmlTemplate('/email/index.html.twig');

        $this->mailer->send($email);
    }
}