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
    public function createConversation(Request $request): JsonResponse
    {
        // Décodage du contenu JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Vérification des champs essentiels
        if (!isset($data['content']) || !isset($data['sender']) || !isset($data['receiver'])) {
            $this->logger->error('Invalid input: Missing content, sender, or receiver');
            return new JsonResponse(['message' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $content = $data['content'];
        $senderId = $data['sender'];
        $receiverId = $data['receiver'];

        $this->logger->info('Received request to create conversation', [
            'senderId' => $senderId,
            'receiverId' => $receiverId,
            'content' => $content
        ]);

        try {
            // Récupérer les utilisateurs par ID
            $sender = $this->entityManager->getRepository(User::class)->find($senderId);
            $receiver = $this->entityManager->getRepository(User::class)->find($receiverId);

            // Vérifier si les utilisateurs existent
            if ($sender === null || $receiver === null) {
                $this->logger->error('Sender or receiver not found', [
                    'senderId' => $senderId,
                    'receiverId' => $receiverId
                ]);
                return new JsonResponse(['message' => 'Sender or receiver not found'], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Créer un nouveau message
            $message = new Message();
            $message->setContent($content);
            $message->setSender($sender);
            $message->setReceiver($receiver);

            // Persister le message en base de données
            $this->entityManager->persist($message);
            $this->entityManager->flush();

            $this->logger->info('Conversation created successfully', ['messageId' => $message->getId()]);

            return new JsonResponse(['message' => 'Conversation created successfully'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            // Gestion des erreurs
            $this->logger->error('Error while creating conversation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return new JsonResponse(['message' => 'An error occurred while creating the conversation'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/conversations", name="list_conversations", methods={"GET"})
     */
    public function listConversations(int $id): JsonResponse
    {
        try {
            // Fetch the user by the given ID
            $user = $this->entityManager->getRepository(User::class)->find($id);
            if (!$user) {
                throw new NotFoundHttpException('User not found');
            }

            // Get personal conversations
            $personalConversations = $this->entityManager->getRepository(Conversation::class)
                ->findBy(['user' => $user]);

            $personalConversationData = [];
            foreach ($personalConversations as $personalConversation) {
                $personalConversationData[] = [
                    'id' => $personalConversation->getId(),
                    'lastMessage' => $personalConversation->getMessage()->getContent(),
                    'name' => $personalConversation->getUser()->getUsername(), // Adjust based on your needs
                    'messages' => [
                        [
                            'text' => $personalConversation->getMessage()->getContent(),
                            'isMine' => $personalConversation->getMessage()->getSender() === $user
                        ]
                    ]
                ];
            }

            // Get group conversations
            $groupConversations = $this->entityManager->getRepository(GroupConversation::class)->findAll();

            $groupConversationData = [];
            foreach ($groupConversations as $groupConversation) {
                $messages = [];
                foreach ($groupConversation->getMessages() as $message) {
                    $messages[] = [
                        'text' => $message->getContent(),
                        'isMine' => $message->getSender() === $user
                    ];
                }

                $groupConversationData[] = [
                    'id' => $groupConversation->getId(),
                    'name' => $groupConversation->getName(),
                    'lastMessage' => $groupConversation->getMessages()->last() ? $groupConversation->getMessages()->last()->getContent() : '',
                    'messages' => $messages
                ];
            }

            return new JsonResponse([
                'personal' => $personalConversationData,
                'group' => $groupConversationData
            ], JsonResponse::HTTP_OK);
        } catch (NotFoundHttpException $e) {
            return new JsonResponse([
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Error while listing conversations: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return new JsonResponse([
                'error' => 'An error occurred while listing conversations'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/group-conversation", name="create_group_conversation", methods={"POST"})
     */
    public function createGroupConversation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || !isset($data['users']) || !isset($data['content']) || !isset($data['sender'])) {
            $this->logger->error('Invalid input: Missing name, users, content, or sender');
            return new JsonResponse(['message' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = $data['name'];
        $userIds = $data['users'];
        $content = $data['content'];
        $senderId = $data['sender'];

        try {
            // Récupérer tous les utilisateurs par leurs IDs
            $users = $this->entityManager->getRepository(User::class)->findBy(['id' => $userIds]);
            if (count($users) !== count($userIds)) {
                $this->logger->error('One or more users not found');
                return new JsonResponse(['message' => 'One or more users not found'], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Récupérer l'utilisateur expéditeur par ID
            $sender = $this->entityManager->getRepository(User::class)->find($senderId);
            if ($sender === null) {
                $this->logger->error('Sender not found. ID: ' . $senderId);
                return new JsonResponse(['message' => 'Sender not found'], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Créer et persister la conversation de groupe
            $groupConversation = new GroupConversation();
            $groupConversation->setName($name);
            foreach ($users as $user) {
                $groupConversation->addUser($user);
            }
            $this->entityManager->persist($groupConversation);
            $this->entityManager->flush();

            // Créer et persister le message
            $message = new Message();
            $message->setContent($content);
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
}
