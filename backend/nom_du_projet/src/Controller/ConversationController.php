<?php

namespace App\Controller;

use App\Entity\GroupConversation;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Conversation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConversationController extends AbstractController

{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/api/conversation", name="create_conversation", methods={"POST"})
     */
// Dans votre méthode de création de conversation
public function createConversation(Request $request): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!isset($data['content'], $data['sender'], $data['receiver'])) {
        return new JsonResponse(['message' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $sender = $this->entityManager->getRepository(User::class)->find($data['sender']);
    $receiver = $this->entityManager->getRepository(User::class)->find($data['receiver']);

    if (!$sender || !$receiver) {
        return new JsonResponse(['message' => 'Sender or receiver not found'], JsonResponse::HTTP_BAD_REQUEST);
    }

    // Rechercher une conversation existante entre les deux utilisateurs
    $conversation = $this->entityManager->getRepository(Conversation::class)->findOneBy([
        'user' => $sender,  // Cela suppose que `user` désigne l'un ou l'autre participant
    ]);

    // Si aucune conversation n'existe, en créer une nouvelle
    if (!$conversation) {
        $conversation = new Conversation();
        $conversation->setUser($sender); // Set l'utilisateur principal de la conversation
        $conversation->setTitle('Conversation between ' . $sender->getUsername() . ' and ' . $receiver->getUsername());

        $this->entityManager->persist($conversation); // Enregistrer la nouvelle conversation
    }

    // Créer un nouveau message
    $message = new Message();
    $message->setContent($data['content']);
    $message->setSender($sender);
    $message->setReceiver($receiver);
    $message->setConversation($conversation); // Assigner le message à la conversation

    $this->entityManager->persist($message);
    $this->entityManager->flush();

    return new JsonResponse(['message' => 'Conversation created successfully'], JsonResponse::HTTP_CREATED);
}

    /**
     * @Route("/api/conversations/{id}", name="list_conversations", methods={"GET"})
     */
    public function listConversations(int $id): JsonResponse
    {
        try {
            $user = $this->fetchUser($id);
            if (!$user) {
                throw new NotFoundHttpException('User not found');
            }

            $personalConversations = $this->entityManager->getRepository(Conversation::class)
                ->findBy(['user' => $user]);

            $personalConversationData = array_map(fn($conversation) => $this->formatPersonalConversation($conversation, $user), $personalConversations);

            $groupConversations = $this->entityManager->getRepository(GroupConversation::class)->findAll();
            $groupConversationData = array_map(fn($group) => $this->formatGroupConversation($group, $user), $groupConversations);

            return new JsonResponse([
                'personal' => $personalConversationData,
                'group' => $groupConversationData
            ], JsonResponse::HTTP_OK);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            $this->logger->error('Error while listing conversations: ' . $e->getMessage(), ['exception' => $e]);
            return new JsonResponse(['error' => 'An error occurred while listing conversations'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/group-conversation", name="create_group_conversation", methods={"POST"})
     */
    public function createGroupConversation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$this->isValidGroupConversationData($data)) {
            $this->logger->error('Invalid input: Missing name, users, content, or sender');
            return new JsonResponse(['message' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $sender = $this->fetchUser($data['sender']);
            if (!$sender) {
                $this->logger->error('Sender not found. ID: ' . $data['sender']);
                return new JsonResponse(['message' => 'Sender not found'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $users = $this->fetchUsers($data['users']);
            if (count($users) !== count($data['users'])) {
                $this->logger->error('One or more users not found');
                return new JsonResponse(['message' => 'One or more users not found'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $groupConversation = new GroupConversation();
            $groupConversation->setName($data['name']);
            foreach ($users as $user) {
                $groupConversation->addUser($user);
            }

            $this->entityManager->persist($groupConversation);
            $this->entityManager->flush();

            $message = new Message();
            $message->setContent($data['content']);
            $message->setSender($sender);
            $message->setGroup($groupConversation);

            $this->entityManager->persist($message);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Group conversation created successfully'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            $this->logger->error('Error while creating group conversation: ' . $e->getMessage());
            return new JsonResponse(['message' => 'An error occurred while creating the group conversation'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function isValidConversationData(array $data): bool
    {
        return isset($data['content'], $data['sender'], $data['receiver']);
    }

    private function isValidGroupConversationData(array $data): bool
    {
        return isset($data['name'], $data['users'], $data['content'], $data['sender']);
    }

    private function fetchUser(int $userId): ?User
    {
        return $this->entityManager->getRepository(User::class)->find($userId);
    }

    private function fetchUsers(array $userIds): array
    {
        return $this->entityManager->getRepository(User::class)->findBy(['id' => $userIds]);
    }

    private function formatPersonalConversation(Conversation $conversation, User $user): array
    {
        $lastMessage = $conversation->getLastMessage();
        $lastMessageContent = $lastMessage ? $lastMessage->getContent() : '';

        $messages = $conversation->getMessages()->map(function ($message) use ($user) {
            return [
                'text' => $message->getContent(),
                'isMine' => $message->getSender() === $user
            ];
        })->toArray();

        return [
            'id' => $conversation->getId(),
            'lastMessage' => $lastMessageContent,
            'name' => $conversation->getUser()->getUsername(),
            'messages' => $messages
        ];
    }

    private function formatGroupConversation(GroupConversation $groupConversation, User $user): array
    {
        $messages = [];
        foreach ($groupConversation->getMessages() as $message) {
            $messages[] = [
                'text' => $message->getContent(),
                'isMine' => $message->getSender() === $user
            ];
        }

        return [
            'id' => $groupConversation->getId(),
            'name' => $groupConversation->getName(),
            'lastMessage' => $groupConversation->getMessages()->last() ? $groupConversation->getMessages()->last()->getContent() : '',
            'messages' => $messages
        ];
    }
}
