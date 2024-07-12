<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class MessageController extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/api/message", name="send_message", methods={"POST"})
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['content']) || !isset($data['sender'])) {
            $this->logger->error('Invalid input: Missing content or sender');
            return new JsonResponse(['message' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $content = $data['content'];
        $senderId = $data['sender'];

        // Log the senderId
        $this->logger->info('Sender ID: ' . $senderId);

        try {
            // Recherche de l'utilisateur par ID
            $sender = $this->entityManager->getRepository(User::class)->find($senderId);

            if ($sender === null) {
                $this->logger->error('Sender not found. ID: ' . $senderId);
                return new JsonResponse(['message' => 'Sender not found'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $this->logger->info('Sender found: ' . $sender->getUsername());

            $message = new Message();
            $message->setContent($content);
            $message->setSender($sender);

            // Log before persisting the message
            $this->logger->info('Persisting the message');

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            // Log after the message is persisted
            $this->logger->info('Message persisted successfully');

            return new JsonResponse(['message' => 'Message sent successfully'], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            $this->logger->error('Error while sending message: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred while sending the message'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/messages", name="api_message_list", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        // Implémentation de la méthode GET pour lister les messages
        $messages = $this->entityManager->getRepository(Message::class)->findAll();

        $response = [];
        foreach ($messages as $message) {
            $response[] = [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'sender' => $message->getSender()->getUsername(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'group' => $message->getGroup() ? $message->getGroup()->getName() : null,
                'receiver' => $message->getReceiver() ? $message->getReceiver()->getUsername() : null,
            ];
        }

        return new JsonResponse($response, JsonResponse::HTTP_OK);
    }
}