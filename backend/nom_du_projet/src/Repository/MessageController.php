<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Commission;
use App\Repository\CommissionRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/api/message', name: 'post_message', methods: ['POST'])]
    public function postMessage(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['content']) || !isset($data['user_id'])) {
            return new JsonResponse(['message' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $message = new Message();
        $message->setContent($data['content']);
        $message->setCreatedAt(new \DateTime());

        // Récupération de l'utilisateur associé
        $user = $entityManager->getRepository(Test1::class)->find($data['user_id']);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], JsonResponse::HTTP_BAD_REQUEST);
        }
        $message->setAuthor($user);

        // Récupération de la commission
        if (isset($data['commission_id'])) {
            $commission = $entityManager->getRepository(Commission::class)->find($data['commission_id']);
            if (!$commission) {
                return new JsonResponse(['message' => 'Commission not found'], JsonResponse::HTTP_BAD_REQUEST);
            }
            $message->setCommission($commission);
        }

        $entityManager->persist($message);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Message posted'], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/messages', name: 'get_messages', methods: ['GET'])]
    public function getMessages(MessageRepository $messageRepository): JsonResponse
    {
        $messages = $messageRepository->findAll();
        $response = [];
        foreach ($messages as $message) {
            $response[] = [
                'content' => $message->getContent(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'author' => $message->getAuthor()->getUsername(),
                'commission' => $message->getCommission() ? $message->getCommission()->getName() : null
            ];
        }

        return new JsonResponse($response);
    }

    // You can add more methods to handle fetching messages by commission, etc...
}