<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Sport;
use App\Entity\Championnat;
use App\Entity\Competition;
use App\Entity\Epreuve;
use App\Repository\EpreuveRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class EpreuveRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private EpreuveRepository $epreuveRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        
        $this->entityManager = $container
            ->get('doctrine')
            ->getManager();
            
        $this->epreuveRepository = $this->entityManager->getRepository(Epreuve::class);

        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        $this->entityManager->close();
        
        parent::tearDown();
    }

    public function testSaveAndFindEpreuve(): void
    {
        $sport = new Sport();
        $sport->setNom('Gymnastique')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Jeux Olympiques');
        
        $sport->addChampionnat($championnat);

        $competition = new Competition();
        $competition->setNom('Artistique');
        
        $championnat->addCompetition($competition);

        $epreuve = new Epreuve();
        $epreuve->setNom('Barres asymétriques');
        
        $competition->addEpreuve($epreuve);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $foundEpreuve = $this->epreuveRepository->find($epreuve->getId());

        $this->assertNotNull($foundEpreuve);
        $this->assertEquals('Barres asymétriques', $foundEpreuve->getNom());
        $this->assertSame($competition->getId(), $foundEpreuve->getCompetition()->getId());
    }

    public function testRemoveEpreuve(): void
    {
        $sport = new Sport();
        $sport->setNom('Judo')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Championnat National');
        
        $sport->addChampionnat($championnat);

        $competition = new Competition();
        $competition->setNom('Catégorie -60kg');
        
        $championnat->addCompetition($competition);

        $epreuve = new Epreuve();
        $epreuve->setNom('Finale');
        
        $competition->addEpreuve($epreuve);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $epreuveId = $epreuve->getId();

        $this->epreuveRepository->remove($epreuve, true);

        $foundEpreuve = $this->epreuveRepository->find($epreuveId);

        $this->assertNull($foundEpreuve);
    }

    public function testSaveMultipleEpreuves(): void
    {
        $sport = new Sport();
        $sport->setNom('Boxe')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Gala de Boxe');
        
        $sport->addChampionnat($championnat);

        $competition = new Competition();
        $competition->setNom('Mi-lourds');
        
        $championnat->addCompetition($competition);

        $epreuve1 = new Epreuve();
        $epreuve1->setNom('Quart de finale');

        $epreuve2 = new Epreuve();
        $epreuve2->setNom('Demi-finale');

        $epreuve3 = new Epreuve();
        $epreuve3->setNom('Finale');

        $competition->addEpreuve($epreuve1);
        $competition->addEpreuve($epreuve2);
        $competition->addEpreuve($epreuve3);
        
        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $this->assertNotNull($epreuve1->getId());
        $this->assertNotNull($epreuve2->getId());
        $this->assertNotNull($epreuve3->getId());
    }
}
