<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Competition;
use App\Entity\Championnat;
use App\Entity\Sport;
use App\Entity\Epreuve;
use App\Repository\CompetitionRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class CompetitionRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private CompetitionRepository $competitionRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
        $this->competitionRepository = $this->entityManager
            ->getRepository(Competition::class);

        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        $this->entityManager->close();
        
        parent::tearDown();
    }

    public function testSaveMethod(): void
    {
        $sport = new Sport();
        $sport->setNom('Gymnastique')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Jeux Olympiques')->setSport($sport);

        $competition = new Competition();
        $competition->setNom('Sol')
                ->setChampionnat($championnat);

        $this->entityManager->persist($sport);
        $this->entityManager->persist($championnat);
        $this->competitionRepository->save($competition, true);

        $this->assertNotNull($competition->getId());
        
        $competitionFromDb = $this->competitionRepository->find($competition->getId());
        $this->assertNotNull($competitionFromDb);
        $this->assertEquals('Sol', $competitionFromDb->getNom());
    }

    public function testRemoveMethod(): void
    {
        $sport = new Sport();
        $sport->setNom('Haltérophilie')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Championnat Mondial')->setSport($sport);

        $competition = new Competition();
        $competition->setNom('Catégorie +105kg')
                ->setChampionnat($championnat);

        $this->entityManager->persist($sport);
        $this->entityManager->persist($championnat);
        $this->competitionRepository->save($competition, true);
        $competitionId = $competition->getId();
        
        $this->competitionRepository->remove($competition, true);

        $competitionFromDb = $this->competitionRepository->find($competitionId);
        $this->assertNull($competitionFromDb);
    }

    public function testFindCompetitionsByChampionnat(): void
    {
        $sport = new Sport();
        $sport->setNom('Ski')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Coupe du Monde')->setSport($sport);

        $competition1 = new Competition();
        $competition1->setNom('Slalom')->setChampionnat($championnat);
        
        $competition2 = new Competition();
        $competition2->setNom('Descente')->setChampionnat($championnat);
        
        $competition3 = new Competition();
        $competition3->setNom('Super-G')->setChampionnat($championnat);

        $this->entityManager->persist($sport);
        $this->entityManager->persist($championnat);
        $this->competitionRepository->save($competition1, false);
        $this->competitionRepository->save($competition2, false);
        $this->competitionRepository->save($competition3, true);

        $competitions = $this->entityManager
            ->getRepository(Competition::class)
            ->findBy(['championnat' => $championnat]);

        $this->assertCount(3, $competitions);
        
        $noms = array_map(fn($c) => $c->getNom(), $competitions);
        $this->assertContains('Slalom', $noms);
        $this->assertContains('Descente', $noms);
        $this->assertContains('Super-G', $noms);
    }

    public function testSaveCompetitionWithEpreuves(): void
    {
        $sport = new Sport();
        $sport->setNom('Athlétisme')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Championnats de France')->setSport($sport);

        $competition = new Competition();
        $competition->setNom('Sprint')->setChampionnat($championnat);

        $epreuve1 = new Epreuve();
        $epreuve1->setNom('100m')->setCompetition($competition);
        
        $epreuve2 = new Epreuve();
        $epreuve2->setNom('200m')->setCompetition($competition);

        $this->entityManager->persist($sport);
        $this->entityManager->persist($championnat);
        $this->competitionRepository->save($competition, true);

        $this->assertNotNull($competition->getId());
        $this->assertNotNull($epreuve1->getId());
        $this->assertNotNull($epreuve2->getId());
        
        $this->entityManager->clear();
        
        $competitionFromDb = $this->competitionRepository->find($competition->getId());
        $this->assertNotNull($competitionFromDb);
        $this->assertCount(2, $competitionFromDb->getEpreuves());
    }
}
