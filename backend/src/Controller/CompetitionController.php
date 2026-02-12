<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Repository\CompetitionRepository;
use App\Repository\ChampionnatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/competitions', name: 'api_competition_')]
class CompetitionController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(CompetitionRepository $competitionRepository): JsonResponse
    {
        $competitions = $competitionRepository->findAll();
        
        return $this->json(array_map(fn($c) => [
            'id' => $c->getId(),
            'nom' => $c->getNom(),
            'championnat' => [
                'id' => $c->getChampionnat()->getId(),
                'nom' => $c->getChampionnat()->getNom(),
                'sport' => [
                    'id' => $c->getChampionnat()->getSport()->getId(),
                    'nom' => $c->getChampionnat()->getSport()->getNom()
                ]
            ],
            'epreuvesCount' => $c->getEpreuves()->count()
        ], $competitions));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Competition $competition): JsonResponse
    {
        return $this->json([
            'id' => $competition->getId(),
            'nom' => $competition->getNom(),
            'championnat' => [
                'id' => $competition->getChampionnat()->getId(),
                'nom' => $competition->getChampionnat()->getNom()
            ],
            'epreuves' => array_map(fn($e) => [
                'id' => $e->getId(),
                'nom' => $e->getNom()
            ], $competition->getEpreuves()->toArray())
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ChampionnatRepository $championnatRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom']) || !isset($data['championnatId'])) {
            return $this->json(['error' => 'Nom et championnatId requis'], Response::HTTP_BAD_REQUEST);
        }

        $championnat = $championnatRepository->find($data['championnatId']);
        if (!$championnat) {
            return $this->json(['error' => 'Championnat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $competition = new Competition();
        $competition->setNom($data['nom']);
        $championnat->addCompetition($competition);

        $em->persist($championnat);
        $em->flush();

        return $this->json([
            'id' => $competition->getId(),
            'nom' => $competition->getNom(),
            'championnat' => [
                'id' => $championnat->getId(),
                'nom' => $championnat->getNom()
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Competition $competition, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($competition);
        $em->flush();

        return $this->json(['message' => 'Compétition supprimée'], Response::HTTP_OK);
    }
}
