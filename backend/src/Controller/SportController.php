<?php

namespace App\Controller;

use App\Entity\Sport;
use App\Repository\SportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/sports', name: 'api_sport_')]
class SportController extends AbstractController
{
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(SportRepository $sportRepository): JsonResponse
    {
        $sports = $sportRepository->findAll();
        
        return $this->json(array_map(fn($sport) => [
            'id' => $sport->getId(),
            'nom' => $sport->getNom(),
            'type' => $sport->getType(),
            'championnatsCount' => $sport->getChampionnats()->count()
        ], $sports));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Sport $sport): JsonResponse
    {
        return $this->json([
            'id' => $sport->getId(),
            'nom' => $sport->getNom(),
            'type' => $sport->getType(),
            'championnats' => array_map(fn($c) => [
                'id' => $c->getId(),
                'nom' => $c->getNom(),
                'competitionsCount' => $c->getCompetitions()->count()
            ], $sport->getChampionnats()->toArray())
        ]);
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom']) || !isset($data['type'])) {
            return $this->json(['error' => 'Nom et type requis'], Response::HTTP_BAD_REQUEST);
        }

        if (!in_array($data['type'], ['individuel', 'collectif'])) {
            return $this->json(['error' => 'Type invalide (individuel ou collectif)'], Response::HTTP_BAD_REQUEST);
        }

        $sport = new Sport();
        $sport->setNom($data['nom']);
        $sport->setType($data['type']);

        $em->persist($sport);
        $em->flush();

        return $this->json([
            'id' => $sport->getId(),
            'nom' => $sport->getNom(),
            'type' => $sport->getType()
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Sport $sport, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($sport);
        $em->flush();

        return $this->json(['message' => 'Sport supprimé'], Response::HTTP_OK);
    }
}
