<?php

namespace App\Controller;

use App\Entity\Epreuve;
use App\Repository\EpreuveRepository;
use App\Repository\CompetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/epreuves', name: 'api_epreuve_')]
class EpreuveController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(EpreuveRepository $epreuveRepository): JsonResponse
    {
        $epreuves = $epreuveRepository->findAll();
        
        return $this->json(array_map(fn($e) => [
            'id' => $e->getId(),
            'nom' => $e->getNom(),
            'competition' => [
                'id' => $e->getCompetition()->getId(),
                'nom' => $e->getCompetition()->getNom(),
                'championnat' => [
                    'id' => $e->getCompetition()->getChampionnat()->getId(),
                    'nom' => $e->getCompetition()->getChampionnat()->getNom()
                ]
            ]
        ], $epreuves));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, CompetitionRepository $competitionRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom']) || !isset($data['competitionId'])) {
            return $this->json(['error' => 'Nom et competitionId requis'], Response::HTTP_BAD_REQUEST);
        }

        $competition = $competitionRepository->find($data['competitionId']);
        if (!$competition) {
            return $this->json(['error' => 'Compétition non trouvée'], Response::HTTP_NOT_FOUND);
        }

        $epreuve = new Epreuve();
        $epreuve->setNom($data['nom']);
        $competition->addEpreuve($epreuve);

        $em->persist($competition);
        $em->flush();

        return $this->json([
            'id' => $epreuve->getId(),
            'nom' => $epreuve->getNom(),
            'competition' => [
                'id' => $competition->getId(),
                'nom' => $competition->getNom()
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Epreuve $epreuve, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($epreuve);
        $em->flush();

        return $this->json(['message' => 'Épreuve supprimée'], Response::HTTP_OK);
    }
}
