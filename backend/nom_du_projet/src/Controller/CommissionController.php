<?php
namespace App\Controller;

use App\Entity\Commission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommissionController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/api/commissions", name="list_commissions", methods={"GET"})
     */
    public function listCommissions(): JsonResponse
    {
        try {
            $commissions = $this->entityManager->getRepository(Commission::class)->findAll();
            $commissionData = [];

            foreach ($commissions as $commission) {
                $commissionData[] = [
                    'id' => $commission->getId(),
                    'name' => $commission->getName(),
                ];
            }

            return new JsonResponse($commissionData, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'An error occurred while retrieving commissions'
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/api/commissions", name="create_commission", methods={"POST"})
     */
    public function createCommission(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!isset($data['name'])) {
                return new JsonResponse(['error' => 'Invalid input'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $commission = new Commission();
            $commission->setName($data['name']);

            $this->entityManager->persist($commission);
            $this->entityManager->flush();

            return new JsonResponse(['message' => 'Commission created successfully', 'commission' => [
                'id' => $commission->getId(),
                'name' => $commission->getName()
            ]], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while creating the commission'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}