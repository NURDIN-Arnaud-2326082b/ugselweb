<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Championnat;
use App\Entity\Sport;
use App\Repository\ChampionnatRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ChampionnatRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private ChampionnatRepository $championnatRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
        $this->championnatRepository = $this->entityManager
            ->getRepository(Championnat::class);

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
        $sport->setNom('Judo')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Championnat de France')
                    ->setSport($sport);

        $this->entityManager->persist($sport);
        $this->championnatRepository->save($championnat, true);

        $this->assertNotNull($championnat->getId());
        
        $championnatFromDb = $this->championnatRepository->find($championnat->getId());
        $this->assertNotNull($championnatFromDb);
        $this->assertEquals('Championnat de France', $championnatFromDb->getNom());
    }

    public function testRemoveMethod(): void
    {
        $sport = new Sport();
        $sport->setNom('Karaté')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Championnat Europe')
                    ->setSport($sport);

        $this->entityManager->persist($sport);
        $this->championnatRepository->save($championnat, true);
        $championnatId = $championnat->getId();
        
        $this->championnatRepository->remove($championnat, true);

        $championnatFromDb = $this->championnatRepository->find($championnatId);
        $this->assertNull($championnatFromDb);
    }

    public function testFindChampionnatsBySport(): void
    {
        $sport = new Sport();
        $sport->setNom('Badminton')->setType('individuel');

        $championnat1 = new Championnat();
        $championnat1->setNom('Open de France')->setSport($sport);
        
        $championnat2 = new Championnat();
        $championnat2->setNom('Championnat National')->setSport($sport);

        $this->entityManager->persist($sport);
        $this->championnatRepository->save($championnat1, false);
        $this->championnatRepository->save($championnat2, true);

        $championnats = $this->entityManager
            ->getRepository(Championnat::class)
            ->findBy(['sport' => $sport]);

        $this->assertCount(2, $championnats);
    }
}
