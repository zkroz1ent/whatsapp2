<?php
namespace App\Controller;

use App\Entity\Commission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;

class CommissionController extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * @Route("/api/commissions", name="list_commissions", methods={"GET"})
     */
    public function listCommissions(): JsonResponse
    {
        try {
            $commissions = $this->entityManager->getRepository(Commission::class)->findAll();

            // Utilize a private method to format commission data
            $commissionData = array_map([$this, 'formatCommission'], $commissions);

            return new JsonResponse($commissionData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Failed to retrieve commissions: ' . $e->getMessage());

            return new JsonResponse([
                'error' => 'An error occurred while retrieving commissions'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/commissions", name="create_commission", methods={"POST"})
     */
    public function createCommission(Request $request, ValidatorInterface $validator): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['name'])) {
                return new JsonResponse(['error' => 'Invalid input: name is required'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $commission = new Commission();
            $commission->setName($data['name']);

            // Validate the commission entity
            $errors = $validator->validate($commission);
            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[] = $error->getMessage();
                }
                return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
            }

            $this->entityManager->persist($commission);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Commission created successfully', 'commission' => $this->formatCommission($commission)], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Failed to create commission: ' . $e->getMessage());

            return new JsonResponse(['error' => 'An error occurred while creating the commission'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Formats a Commission entity into an array suitable for JSON response.
     */
    private function formatCommission(Commission $commission): array
    {
        return [
            'id' => $commission->getId(),
            'name' => $commission->getName(),
        ];
    }
}