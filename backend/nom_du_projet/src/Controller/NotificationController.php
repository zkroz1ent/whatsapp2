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

class NotificationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/notifications", name="get_notifications", methods={"GET"})
     */
    public function getNotifications(): JsonResponse
    {
        try {
            $user = $this->getUser();
            $notifications = $this->entityManager->getRepository(Notification::class)->findByUser($user);

            $notificationData = [];
            foreach ($notifications as $notif) {
                $notificationData[] = [
                    'id' => $notif->getId(),
                    'content' => $notif->getMessageContent(),
                    'createdAt' => $notif->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }

            return new JsonResponse($notificationData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
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

            $user = $this->getUser();

            // Clear existing notifications
            $existingNotifications = $this->entityManager->getRepository(Notification::class)->findBy(['users' => $user]);
            foreach ($existingNotifications as $notif) {
                $notif->removeUser($user);
                if ($notif->getUsers()->isEmpty()) {
                    $this->entityManager->remove($notif);
                }
            }
            $this->entityManager->flush();

            // Add new notifications
            foreach ($data['notifications'] as $notifData) {
                $notif = new Notification();
                $notif->addUser($user);

                if ($notifData['type'] === 'global') {
                    $notif->setMessageContent('Global Notification');
                } else {
                    $commission = $this->entityManager->getRepository(Commission::class)->find($notifData['id']);
                    if (!$commission) {
                        return new JsonResponse(['error' => 'Invalid commission'], JsonResponse::HTTP_BAD_REQUEST);
                    }
                    $notif->setMessageContent('Notification for ' . $commission->getName());
                }

                $this->entityManager->persist($notif);
            }

            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Notifications updated successfully'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while updating notifications'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}