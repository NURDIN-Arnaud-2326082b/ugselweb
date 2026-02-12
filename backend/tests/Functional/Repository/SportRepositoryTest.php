<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Sport;
use App\Repository\SportRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class SportRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private SportRepository $sportRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
        $this->sportRepository = $this->entityManager->getRepository(Sport::class);

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
        $sport->setNom('Rugby')->setType('collectif');

        $this->sportRepository->save($sport, true);

        $this->assertNotNull($sport->getId());
        
        $sportFromDb = $this->sportRepository->find($sport->getId());
        $this->assertNotNull($sportFromDb);
        $this->assertEquals('Rugby', $sportFromDb->getNom());
    }

    public function testRemoveMethod(): void
    {
        $sport = new Sport();
        $sport->setNom('Escrime')->setType('individuel');

        $this->sportRepository->save($sport, true);
        $sportId = $sport->getId();
        
        $this->sportRepository->remove($sport, true);

        $sportFromDb = $this->sportRepository->find($sportId);
        $this->assertNull($sportFromDb);
    }

    public function testFindByTypeIndividuel(): void
    {
        $tennis = new Sport();
        $tennis->setNom('Tennis')->setType('individuel');
        
        $golf = new Sport();
        $golf->setNom('Golf')->setType('individuel');
        
        $volleyball = new Sport();
        $volleyball->setNom('Volleyball')->setType('collectif');

        $this->sportRepository->save($tennis, false);
        $this->sportRepository->save($golf, false);
        $this->sportRepository->save($volleyball, true);

        $sportsIndividuels = $this->sportRepository->findByType('individuel');

        $this->assertGreaterThanOrEqual(2, count($sportsIndividuels));
        
        foreach ($sportsIndividuels as $sport) {
            $this->assertEquals('individuel', $sport->getType());
        }
    }

    public function testFindByTypeCollectif(): void
    {
        $handball = new Sport();
        $handball->setNom('Handball')->setType('collectif');
        
        $waterPolo = new Sport();
        $waterPolo->setNom('Water-polo')->setType('collectif');

        $this->sportRepository->save($handball, false);
        $this->sportRepository->save($waterPolo, true);

        $sportsCollectifs = $this->sportRepository->findByType('collectif');

        $this->assertGreaterThanOrEqual(2, count($sportsCollectifs));
        
        foreach ($sportsCollectifs as $sport) {
            $this->assertEquals('collectif', $sport->getType());
        }
    }

    public function testFindByTypeReturnsOrderedResults(): void
    {
        $boxe = new Sport();
        $boxe->setNom('Boxe')->setType('individuel');
        
        $athletisme = new Sport();
        $athletisme->setNom('Athlétisme')->setType('individuel');
        
        $cyclisme = new Sport();
        $cyclisme->setNom('Cyclisme')->setType('individuel');

        $this->sportRepository->save($boxe, false);
        $this->sportRepository->save($athletisme, false);
        $this->sportRepository->save($cyclisme, true);

        $sports = $this->sportRepository->findByType('individuel');

        $previousName = '';
        foreach ($sports as $sport) {
            $this->assertGreaterThanOrEqual($previousName, $sport->getNom());
            $previousName = $sport->getNom();
        }
    }
}
