<?php
namespace App\Controller;

use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/api/message", name="send_message", methods={"POST"})
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['content'])) {
            return new JsonResponse(['message' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->getUser();
        // if (!$user) {
        //     return new JsonResponse(['message' => 'Unauthorized'], JsonResponse::HTTP_UNAUTHORIZED);
        // }

        $message = new Message();
        $message->setContent($data['content']);
        $message->setSender($user);

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Message sent successfully'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/messages", name="api_message_list", methods={"GET"})
     */
    public function list(): JsonResponse
    {
        // Implémentation de la méthode GET pour lister les messages
        return new JsonResponse(['message' => 'Listing messages'], JsonResponse::HTTP_OK);
    }
}