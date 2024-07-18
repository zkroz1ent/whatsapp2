<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Commission;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/message", name="send_message", methods={"POST"})
     */
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['content']) || !isset($data['sender'])) {
                return new JsonResponse(['error' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $content = $data['content'];
            $senderId = $data['sender'];
            $commissionId = $data['commissionId'] ?? null;
            $isGlobal = $data['isGlobal'] ?? false;

            $sender = $this->entityManager->getRepository(User::class)->find($senderId);

            if (!$sender) {
                return new JsonResponse(['error' => 'Sender not found'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $message = new Message();
            $message->setContent($content);
            $message->setSender($sender);
            $message->setIsGlobal($isGlobal);

            if ($commissionId) {
                $commission = $this->entityManager->getRepository(Commission::class)->find($commissionId);
                if ($commission) {
                    $message->setCommission($commission);
                } else {
                    return new JsonResponse(['error' => 'Commission not found'], JsonResponse::HTTP_BAD_REQUEST);
                }
            }

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            // Créer des notifications pour les utilisateurs concernés
            $this->createNotifications($message, $isGlobal, $commission);

            return new JsonResponse(['message' => 'Message sent successfully'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while sending the message'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Crée des notifications pour les utilisateurs en fonction des préférences de notifications.
     */
    private function createNotifications(Message $message, bool $isGlobal, ?Commission $commission = null): void
    {
        $users = [];
        if ($isGlobal) {
            // Récupérer tous les utilisateurs
            $users = $this->entityManager->getRepository(User::class)->findAll();
        } elseif ($commission) {
            // Récupérer les utilisateurs abonnés à la commission
            $users = $commission->getUsers();
        }

        foreach ($users as $user) {
            $notification = new Notification();
            $notification->setMessage($message);
            $notification->setMessageContent($message->getContent());
            $notification->addUser($user);

            $this->entityManager->persist($notification);
        }

        $this->entityManager->flush();
    }

    /**
     * @Route("/api/messages/global", name="get_global_messages", methods={"GET"})
     */
    public function getGlobalMessages(): JsonResponse
    {
        try {
            $messages = $this->entityManager->getRepository(Message::class)
                ->findBy(['isGlobal' => true], ['id' => 'DESC']);

            $messageData = [];
            foreach ($messages as $message) {
                $messageData[] = [
                    'id' => $message->getId(),
                    'content' => $message->getContent(),
                    'sender' => $message->getSender()->getUsername()
                ];
            }

            return new JsonResponse($messageData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while retrieving global messages'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/messages/commission/{id}", name="get_commission_messages", methods={"GET"})
     */
    public function getCommissionMessages(int $id): JsonResponse
    {
        try {
            $commission = $this->entityManager->getRepository(Commission::class)->find($id);

            if (!$commission) {
                return new JsonResponse(['error' => 'Commission not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            $messages = $this->entityManager->getRepository(Message::class)
                ->findBy(['commission' => $commission], ['id' => 'DESC']);

            $messageData = [];
            foreach ($messages as $message) {
                $messageData[] = [
                    'id' => $message->getId(),
                    'content' => $message->getContent(),
                    'sender' => $message->getSender()->getUsername()
                ];
            }

            return new JsonResponse($messageData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while retrieving commission messages'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/notifications/{messageId}", name="get_notifications_for_message", methods={"GET"})
     */
    public function getNotificationsForMessage(int $messageId): JsonResponse
    {
        try {
            $message = $this->entityManager->getRepository(Message::class)->find($messageId);

            if (!$message) {
                return new JsonResponse(['error' => 'Message not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            $notifications = $this->entityManager->getRepository(Notification::class)->findBy(['message' => $message]);

            $notificationData = [];
            foreach ($notifications as $notification) {
                $notificationData[] = [
                    'id' => $notification->getId(),
                    'content' => $notification->getMessageContent(),
                    'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }

            return new JsonResponse($notificationData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while retrieving notifications'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}