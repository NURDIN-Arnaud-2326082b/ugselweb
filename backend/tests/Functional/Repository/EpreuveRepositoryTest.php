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
        $championnat->setNom('Jeux Olympiques')->setSport($sport);

        $competition = new Competition();
        $competition->setNom('Artistique')->setChampionnat($championnat);

        $epreuve = new Epreuve();
        $epreuve->setNom('Barres asymétriques')->setCompetition($competition);

        $this->epreuveRepository->save($epreuve);
        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $foundEpreuve = $this->epreuveRepository->find($epreuve->getId());

        $this->assertNotNull($foundEpreuve);
        $this->assertEquals('Barres asymétriques', $foundEpreuve->getNom());
        $this->assertSame($competition, $foundEpreuve->getCompetition());
    }

    public function testRemoveEpreuve(): void
    {
        $sport = new Sport();
        $sport->setNom('Judo')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Championnat National')->setSport($sport);

        $competition = new Competition();
        $competition->setNom('Catégorie -60kg')->setChampionnat($championnat);

        $epreuve = new Epreuve();
        $epreuve->setNom('Finale')->setCompetition($competition);

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
        $championnat->setNom('Gala de Boxe')->setSport($sport);

        $competition = new Competition();
        $competition->setNom('Mi-lourds')->setChampionnat($championnat);

        $epreuve1 = new Epreuve();
        $epreuve1->setNom('Quart de finale')->setCompetition($competition);

        $epreuve2 = new Epreuve();
        $epreuve2->setNom('Demi-finale')->setCompetition($competition);

        $epreuve3 = new Epreuve();
        $epreuve3->setNom('Finale')->setCompetition($competition);

        $this->epreuveRepository->save($epreuve1);
        $this->epreuveRepository->save($epreuve2);
        $this->epreuveRepository->save($epreuve3);
        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $this->assertNotNull($epreuve1->getId());
        $this->assertNotNull($epreuve2->getId());
        $this->assertNotNull($epreuve3->getId());
    }
}
