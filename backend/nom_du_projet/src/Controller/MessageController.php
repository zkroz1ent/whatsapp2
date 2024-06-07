<?php

namespace App\Controller;

use App\Entity\Message;
use App\Repository\UserRepository;
use App\Repository\CommissionRepository;
use App\Repository\Test1Repository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MessageController extends AbstractController
{
    #[Route('/api/message', name: 'api_message_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Test1Repository $userRepository, CommissionRepository $commissionRepository, SerializerInterface $serializer): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['content']) || !isset($data['commission_id'])) {
            return new Response('Invalid data', Response::HTTP_BAD_REQUEST);
        }

        $message = new Message();
        $message->setContent($data['content']);
        $message->setCreatedAt(new \DateTime());

        $author = $this->getUser();
        $commission = $commissionRepository->find($data['commission_id']);

        if (!$author) {
            return new Response('Author not found', Response::HTTP_BAD_REQUEST);
        }
        if (!$commission) {
            return new Response('Commission not found', Response::HTTP_BAD_REQUEST);
        }

        $message->setAuthor($author);
        $message->setCommission($commission);

        $entityManager->persist($message);
        $entityManager->flush();

        $response_data = $serializer->serialize($message, 'json');
        return new Response($response_data, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }

    #[Route('/api/messages', name: 'api_message_list', methods: ['GET'])]
    public function list(EntityManagerInterface $entityManager, SerializerInterface $serializer): Response
    {
        $messages = $entityManager->getRepository(Message::class)->findAll();
        $response_data = $serializer->serialize($messages, 'json');

        return new Response($response_data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}