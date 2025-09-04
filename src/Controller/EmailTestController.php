<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class EmailTestController extends AbstractController
{
    #[Route('/test-email', name: 'test_email', methods: ['GET'])]
    public function testEmail(MailerInterface $mailer): JsonResponse
    {
        try {
            $email = (new Email())
                ->from('noreply@skybrokersystem.com')
                ->to('test@example.com')
                ->subject('SkyBrokerSystem v2 - Test Email')
                ->text('To jest testowy email z SkyBrokerSystem v2!')
                ->html('<h1>Test Email</h1><p>To jest testowy email z <strong>SkyBrokerSystem v2</strong>!</p><p>System działa poprawnie.</p>');

            $mailer->send($email);

            return $this->json([
                'success' => true,
                'message' => 'Email wysłany pomyślnie!',
                'mailhog_ui' => 'http://185.213.25.106:8025',
                'details' => [
                    'from' => 'noreply@skybrokersystem.com',
                    'to' => 'test@example.com',
                    'subject' => 'SkyBrokerSystem v2 - Test Email'
                ]
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Błąd wysyłania emaila: ' . $e->getMessage()
            ], 500);
        }
    }

    #[Route('/send-email', name: 'send_email', methods: ['POST'])]
    public function sendEmail(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json([
                'success' => false,
                'message' => 'Błędny format JSON'
            ], 400);
        }
        
        $to = $data['to'] ?? null;
        $subject = $data['subject'] ?? 'Test Email';
        $message = $data['message'] ?? 'Test message';

        if (!$to) {
            return $this->json([
                'success' => false,
                'message' => 'Adres email jest wymagany'
            ], 400);
        }

        try {
            $email = (new Email())
                ->from('noreply@skybrokersystem.com')
                ->to($to)
                ->subject($subject)
                ->text(strip_tags($message))
                ->html("<h1>{$subject}</h1><p>{$message}</p><hr><small>Wysłane z SkyBrokerSystem v2</small>");

            $mailer->send($email);

            return $this->json([
                'success' => true,
                'message' => 'Email wysłany pomyślnie!',
                'mailhog_ui' => 'http://185.213.25.106:8025'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Błąd wysyłania emaila: ' . $e->getMessage()
            ], 500);
        }
    }
}