<?php

namespace App\Controller;

use App\Entity\Championnat;
use App\Repository\ChampionnatRepository;
use App\Repository\SportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/championnats', name: 'api_championnat_')]
class ChampionnatController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(ChampionnatRepository $championnatRepository): JsonResponse
    {
        $championnats = $championnatRepository->findAll();
        
        return $this->json(array_map(fn($c) => [
            'id' => $c->getId(),
            'nom' => $c->getNom(),
            'sport' => [
                'id' => $c->getSport()->getId(),
                'nom' => $c->getSport()->getNom()
            ],
            'competitionsCount' => $c->getCompetitions()->count()
        ], $championnats));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Championnat $championnat): JsonResponse
    {
        return $this->json([
            'id' => $championnat->getId(),
            'nom' => $championnat->getNom(),
            'sport' => [
                'id' => $championnat->getSport()->getId(),
                'nom' => $championnat->getSport()->getNom()
            ],
            'competitions' => array_map(fn($comp) => [
                'id' => $comp->getId(),
                'nom' => $comp->getNom(),
                'epreuvesCount' => $comp->getEpreuves()->count()
            ], $championnat->getCompetitions()->toArray())
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, SportRepository $sportRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom']) || !isset($data['sportId'])) {
            return $this->json(['error' => 'Nom et sportId requis'], Response::HTTP_BAD_REQUEST);
        }

        $sport = $sportRepository->find($data['sportId']);
        if (!$sport) {
            return $this->json(['error' => 'Sport non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $championnat = new Championnat();
        $championnat->setNom($data['nom']);
        $sport->addChampionnat($championnat);

        $em->persist($sport);
        $em->flush();

        return $this->json([
            'id' => $championnat->getId(),
            'nom' => $championnat->getNom(),
            'sport' => [
                'id' => $sport->getId(),
                'nom' => $sport->getNom()
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Championnat $championnat, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($championnat);
        $em->flush();

        return $this->json(['message' => 'Championnat supprimé'], Response::HTTP_OK);
    }
}
