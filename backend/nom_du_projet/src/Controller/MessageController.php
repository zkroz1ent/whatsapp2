<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Commission;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface; // Ajout du service de logging

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
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data || !isset($data['content'], $data['sender'])) {
                return new JsonResponse(['error' => 'Invalid input data'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $sender = $this->entityManager->getRepository(User::class)->find($data['sender']);
            if (!$sender) {
                return new JsonResponse(['error' => 'Sender not found'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $message = new Message();
            $message->setContent($data['content']);
            $message->setSender($sender);
            $message->setIsGlobal($data['isGlobal'] ?? false);

            if (isset($data['commissionId'])) {
                $commission = $this->entityManager->getRepository(Commission::class)->find($data['commissionId']);
                if ($commission) {
                    $message->setCommission($commission);
                } else {
                    return new JsonResponse(['error' => 'Commission not found'], JsonResponse::HTTP_BAD_REQUEST);
                }
            }

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $this->createNotifications($message, $data['isGlobal'] ?? false, $message->getCommission());

            return new JsonResponse(['message' => 'Message sent successfully'], JsonResponse::HTTP_CREATED);
        } catch (NoResultException $e) {
            $this->logger->error('No result found: ' . $e->getMessage());
            return new JsonResponse(['error' => 'No result found'], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            $this->logger->error('Error sending message: ' . $e->getMessage());
            return new JsonResponse(['error' => 'An error occurred while sending the message'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function createNotifications(Message $message, bool $isGlobal, ?Commission $commission = null): void
    {
        $users = $isGlobal
            ? $this->entityManager->getRepository(User::class)->findAll()
            : ($commission ? $commission->getUsers() : []);

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

            $messageData = array_map(fn($msg) => [
                'id' => $msg->getId(),
                'content' => $msg->getContent(),
                'sender' => $msg->getSender()->getUsername()
            ], $messages);

            return new JsonResponse($messageData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving global messages: ' . $e->getMessage());
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

            $messageData = array_map(fn($msg) => [
                'id' => $msg->getId(),
                'content' => $msg->getContent(),
                'sender' => $msg->getSender()->getUsername()
            ], $messages);

            return new JsonResponse($messageData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving commission messages: ' . $e->getMessage());
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

            $notificationData = array_map(fn($notification) => [
                'id' => $notification->getId(),
                'content' => $notification->getMessageContent(),
                'createdAt' => $notification->getCreatedAt()->format('Y-m-d H:i:s')
            ], $notifications);

            return new JsonResponse($notificationData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error retrieving notifications for message: ' . $e->getMessage());
            return new JsonResponse(['error' => 'An error occurred while retrieving notifications'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}