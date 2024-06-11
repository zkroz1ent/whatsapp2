<?php
// src/Controller/MessageController.php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Test1;
use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MessageController extends AbstractController
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }
    
    // Nouvelle Méthode pour obtenir un utilisateur simulé
    private function getFakeUser(): Test1
    {
        // Vous pouvez ici récupérer un utilisateur existant dans la base de données
        // pour simplifier, imaginez que l'ID de l'utilisateur est 1
        return $this->entityManager->getRepository(Test1::class)->find(1);
    }

    /**
     * @Route("/api/messages", name="send_message", methods={"POST"})
     */
    public function sendMessage(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $content = $data['content'] ?? null;
        $receiverId = $data['receiverId'] ?? null;

        if (!$content || !$receiverId) {
            return $this->json(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $message = new Message();
        $message->setContent($content);
        
        // Utilisation de l'user simulé
        $sender = $this->getFakeUser();
        
        // Assurez-vous que getUser() retourne un utilisateur simulé ou authentifié
        // $sender = $this->getUser() ?? $sender;
        
        if(!$sender) {
            return $this->json(['error' => 'User not authenticated'], Response::HTTP_UNAUTHORIZED);
        }
        
        $message->setSender($sender);
        $message->setReceiverId($receiverId);
        $message->setSentAt(new \DateTime());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $this->json(['status' => 'Message sent!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/messages", name="get_messages", methods={"GET"})
     */
    public function getMessages(MessageRepository $messageRepository): Response
    {
        $messages = $messageRepository->findAll();
        return $this->json($messages);
    }
}