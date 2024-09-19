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
            $user = $this->entityManager->getRepository(User::class)->find($userId);
            if (!$user) {
                return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            $notifications = $this->entityManager->getRepository(Notification::class)->findBy(['users' => $user]);

            $notificationData = array_map(fn($notif) => [
                'id' => $notif->getId(),
                'content' => $notif->getMessageContent(),
                'createdAt' => $notif->getCreatedAt()->format('Y-m-d H:i:s')
            ], $notifications);

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
        $this->entityManager->beginTransaction();

        try {
            $data = json_decode($request->getContent(), true);
            if (!$data || !isset($data['userId'], $data['notifications'])) {
                return new JsonResponse(['error' => 'Invalid data'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $user = $this->entityManager->getRepository(User::class)->find($data['userId']);
            if (!$user) {
                return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
            }

            $this->clearUserNotifications($user);
            $this->addNewNotifications($user, $data['notifications']);

            $this->entityManager->commit();
            return new JsonResponse(['message' => 'Notifications updated successfully'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error during the update process', ['exception' => $e]);
            return new JsonResponse(['error' => 'An error occurred while updating notifications'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function clearUserNotifications(User $user): void
    {
        $existingNotifications = $this->entityManager->getRepository(Notification::class)->findBy(['users' => $user]);

        foreach ($existingNotifications as $notif) {
            $notif->removeUser($user);
            if ($notif->getUsers()->isEmpty()) {
                $this->entityManager->remove($notif);
            }
        }
        $this->entityManager->flush();
    }

    private function addNewNotifications(User $user, array $notifications): void
    {
        foreach ($notifications as $notifData) {
            if ($notifData['type'] === 'global') {
                $notif = new Notification();
                $notif->setMessageContent('Global Notification');
            } else {
                $commission = $this->entityManager->getRepository(Commission::class)->find($notifData['id']);
                if (!$commission) {
                    throw new \Exception('Invalid commission');
                }
                $notif = new Notification();
                $notif->setMessageContent('Notification for ' . $commission->getName());
            }
            $notif->addUser($user);
            $this->entityManager->persist($notif);
        }
        $this->entityManager->flush();
    }
}