<?php
namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Entity\Commission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class NotificationController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/api/notifications/{userId}", name="get_notifications", methods={"GET"})
     */
    public function getNotifications(int $userId): JsonResponse
    {
        try {
            $this->logger->info('Fetching notifications for user', ['user_id' => $userId]);
            $user = $this->entityManager->getRepository(User::class)->find($userId);

            if (!$user) {
                $this->logger->error('User not found', ['user_id' => $userId]);
                return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            $notifications = $this->entityManager->getRepository(Notification::class)->findBy(['users' => $user]);
            $notificationData = [];
            foreach ($notifications as $notif) {
                $notificationData[] = [
                    'id' => $notif->getId(),
                    'content' => $notif->getMessageContent(),
                    'createdAt' => $notif->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }

            $this->logger->info('Fetched notifications successfully', ['count' => count($notificationData)]);
            return new JsonResponse($notificationData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error while fetching notifications', ['exception' => $e]);
            return new JsonResponse(['error' => 'An error occurred while retrieving notifications'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/notifications", name="update_notifications", methods={"POST"})
     */
    public function updateNotifications(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $this->logger->info('Updating notifications', ['data' => $data]);

            if (!isset($data['userId'])) {
                $this->logger->error('User ID not provided');
                return new JsonResponse(['error' => 'User ID not provided'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $user = $this->entityManager->getRepository(User::class)->find($data['userId']);
            if (!$user) {
                $this->logger->error('User not found', ['user_id' => $data['userId']]);
                return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            // Clear existing notifications
            $existingNotifications = $this->entityManager->getRepository(Notification::class)->findBy(['users' => $user]);
            foreach ($existingNotifications as $notif) {
                if ($notif->getUsers()->contains($user)) {
                    $notif->removeUser($user);
                    if ($notif->getUsers()->isEmpty()) {
                        $this->logger->info('Removing notification', ['notification_id' => $notif->getId()]);
                        $this->entityManager->remove($notif);
                    }
                }
            }
            $this->entityManager->flush();
            $this->logger->info('Cleared existing notifications');

            // Add new notifications
            foreach ($data['notifications'] as $notifData) {
                $notif = new Notification();

                if ($notifData['type'] === 'global') {
                    $notif->setMessageContent('Global Notification');
                } else {
                    $commission = $this->entityManager->getRepository(Commission::class)->find($notifData['id']);
                    if (!$commission) {
                        $this->logger->error('Commission not found', ['commission_id' => $notifData['id']]);
                        return new JsonResponse(['error' => 'Invalid commission'], JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $notif->setMessageContent('Notification for ' . $commission->getName());
                }

                $notif->addUser($user);
                $this->entityManager->persist($notif);
                $this->logger->info('Added new notification', ['notification_id' => $notif->getId()]);
            }

            $this->entityManager->flush();
            $this->logger->info('New notifications persisted successfully');

            return new JsonResponse(['message' => 'Notifications updated successfully'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $this->logger->error('Error while updating notifications', ['exception' => $e]);
            return new JsonResponse(['error' => 'An error occurred while updating notifications'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}