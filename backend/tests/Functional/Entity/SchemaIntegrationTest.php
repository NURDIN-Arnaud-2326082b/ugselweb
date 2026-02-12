<?php

namespace App\Tests\Functional\Entity;

use App\Entity\Sport;
use App\Entity\Championnat;
use App\Entity\Competition;
use App\Entity\Epreuve;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class SchemaIntegrationTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        $this->entityManager->close();
        
        parent::tearDown();
    }

    public function testPersistAndRetrieveSport(): void
    {
        $sport = new Sport();
        $sport->setNom('Football')->setType('collectif');

        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $this->assertNotNull($sport->getId());

        $sportFromDb = $this->entityManager
            ->getRepository(Sport::class)
            ->find($sport->getId());

        $this->assertNotNull($sportFromDb);
        $this->assertEquals('Football', $sportFromDb->getNom());
        $this->assertEquals('collectif', $sportFromDb->getType());
    }

    public function testPersistSportWithChampionnats(): void
    {
        $sport = new Sport();
        $sport->setNom('Athlétisme')->setType('individuel');

        $championnat1 = new Championnat();
        $championnat1->setNom('Championnat National');
        
        $championnat2 = new Championnat();
        $championnat2->setNom('Coupe Régionale');

        $sport->addChampionnat($championnat1);
        $sport->addChampionnat($championnat2);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $sportFromDb = $this->entityManager
            ->getRepository(Sport::class)
            ->find($sport->getId());

        $this->assertNotNull($sportFromDb);
        $this->assertCount(2, $sportFromDb->getChampionnats());
        
        $championnatNames = $sportFromDb->getChampionnats()->map(
            fn($c) => $c->getNom()
        )->toArray();
        
        $this->assertContains('Championnat National', $championnatNames);
        $this->assertContains('Coupe Régionale', $championnatNames);
    }

    public function testPersistChampionnatWithCompetitions(): void
    {
        $sport = new Sport();
        $sport->setNom('Basketball')->setType('collectif');

        $championnat = new Championnat();
        $championnat->setNom('Championnat de France');
        
        $sport->addChampionnat($championnat);

        $competition1 = new Competition();
        $competition1->setNom('Poule A');
        
        $competition2 = new Competition();
        $competition2->setNom('Poule B');
        
        $competition3 = new Competition();
        $competition3->setNom('Finale');

        $championnat->addCompetition($competition1);
        $championnat->addCompetition($competition2);
        $championnat->addCompetition($competition3);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();
        
        $championnatId = $championnat->getId();
        $this->entityManager->clear();

        $championnatFromDb = $this->entityManager
            ->getRepository(Championnat::class)
            ->find($championnatId);

        $this->assertNotNull($championnatFromDb);
        $this->assertCount(3, $championnatFromDb->getCompetitions());
        
        $competitionNames = $championnatFromDb->getCompetitions()->map(
            fn($c) => $c->getNom()
        )->toArray();
        
        $this->assertContains('Poule A', $competitionNames);
        $this->assertContains('Poule B', $competitionNames);
        $this->assertContains('Finale', $competitionNames);
    }

    public function testCompleteHierarchySportChampionnatCompetition(): void
    {
        $sport = new Sport();
        $sport->setNom('Tennis')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Roland Garros')
                    ->setSport($sport);

        $competition1 = new Competition();
        $competition1->setNom('Simple Messieurs')
                     ->setChampionnat($championnat);
        
        $competition2 = new Competition();
        $competition2->setNom('Simple Dames')
                     ->setChampionnat($championnat);

        $this->entityManager->persist($sport);
        $this->entityManager->persist($championnat);
        $this->entityManager->persist($competition1);
        $this->entityManager->persist($competition2);
        $this->entityManager->flush();

        $sportId = $sport->getId();
        $this->entityManager->clear();

        $sportFromDb = $this->entityManager
            ->getRepository(Sport::class)
            ->find($sportId);

        $this->assertNotNull($sportFromDb);
        $this->assertEquals('Tennis', $sportFromDb->getNom());
        $this->assertCount(1, $sportFromDb->getChampionnats());

        $championnatFromDb = $sportFromDb->getChampionnats()->first();
        $this->assertEquals('Roland Garros', $championnatFromDb->getNom());
        $this->assertCount(2, $championnatFromDb->getCompetitions());

        foreach ($championnatFromDb->getCompetitions() as $competition) {
            $this->assertSame($championnatFromDb, $competition->getChampionnat());
        }
    }

    public function testCascadeRemoveSportDeletesChampionnatsAndCompetitions(): void
    {
        $sport = new Sport();
        $sport->setNom('Rugby')->setType('collectif');

        $championnat = new Championnat();
        $championnat->setNom('Top 14');
        $sport->addChampionnat($championnat);

        $competition = new Competition();
        $competition->setNom('Phase régulière');
        $championnat->addCompetition($competition);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $championnatId = $championnat->getId();
        $competitionId = $competition->getId();

        $this->entityManager->remove($sport);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $championnatFromDb = $this->entityManager
            ->getRepository(Championnat::class)
            ->find($championnatId);
        
        $competitionFromDb = $this->entityManager
            ->getRepository(Competition::class)
            ->find($competitionId);

        $this->assertNull($championnatFromDb);
        $this->assertNull($competitionFromDb);
    }

    public function testRemoveChampionnatDeletesCompetitions(): void
    {
        $sport = new Sport();
        $sport->setNom('Volley-ball')->setType('collectif');

        $championnat = new Championnat();
        $championnat->setNom('Championnat Régional');
        $sport->addChampionnat($championnat);

        $competition = new Competition();
        $competition->setNom('Phase de poules');
        $championnat->addCompetition($competition);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $competitionId = $competition->getId();

        $sport->removeChampionnat($championnat);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $competitionFromDb = $this->entityManager
            ->getRepository(Competition::class)
            ->find($competitionId);

        $this->assertNull($competitionFromDb);
    }

    public function testQuerySportByType(): void
    {
        $football = new Sport();
        $football->setNom('Football')->setType('collectif');
        
        $tennis = new Sport();
        $tennis->setNom('Tennis')->setType('individuel');
        
        $basketball = new Sport();
        $basketball->setNom('Basketball')->setType('collectif');

        $this->entityManager->persist($football);
        $this->entityManager->persist($tennis);
        $this->entityManager->persist($basketball);
        $this->entityManager->flush();

        $sportsCollectifs = $this->entityManager
            ->getRepository(Sport::class)
            ->findByType('collectif');

        $this->assertCount(2, $sportsCollectifs);
        
        $noms = array_map(fn($s) => $s->getNom(), $sportsCollectifs);
        $this->assertContains('Football', $noms);
        $this->assertContains('Basketball', $noms);
        $this->assertNotContains('Tennis', $noms);
    }

    public function testBidirectionalRelationshipConsistency(): void
    {
        $sport = new Sport();
        $sport->setNom('Handball')->setType('collectif');

        $championnat = new Championnat();
        $championnat->setNom('Coupe de France');

        $sport->addChampionnat($championnat);

        $this->assertSame($sport, $championnat->getSport());
        $this->assertTrue($sport->getChampionnats()->contains($championnat));

        $competition = new Competition();
        $competition->setNom('Quart de finale');

        $championnat->addCompetition($competition);

        $this->assertSame($championnat, $competition->getChampionnat());
        $this->assertTrue($championnat->getCompetitions()->contains($competition));
    }

    public function testPersistCompetitionWithEpreuves(): void
    {
        $sport = new Sport();
        $sport->setNom('Athlétisme')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Championnats de France');
        
        $sport->addChampionnat($championnat);

        $competition = new Competition();
        $competition->setNom('Sprint');
        
        $championnat->addCompetition($competition);

        $epreuve1 = new Epreuve();
        $epreuve1->setNom('100m');
        
        $epreuve2 = new Epreuve();
        $epreuve2->setNom('200m');
        
        $epreuve3 = new Epreuve();
        $epreuve3->setNom('400m');

        $competition->addEpreuve($epreuve1);
        $competition->addEpreuve($epreuve2);
        $competition->addEpreuve($epreuve3);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();
        
        $competitionId = $competition->getId();
        $this->entityManager->clear();

        $competitionFromDb = $this->entityManager
            ->getRepository(Competition::class)
            ->find($competitionId);

        $this->assertNotNull($competitionFromDb);
        $this->assertCount(3, $competitionFromDb->getEpreuves());
        
        $epreuveNames = $competitionFromDb->getEpreuves()->map(
            fn($e) => $e->getNom()
        )->toArray();
        
        $this->assertContains('100m', $epreuveNames);
        $this->assertContains('200m', $epreuveNames);
        $this->assertContains('400m', $epreuveNames);
    }

    public function testCompleteHierarchySportChampionnatCompetitionEpreuve(): void
    {
        $sport = new Sport();
        $sport->setNom('Natation')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Championnats du Monde');
        
        $sport->addChampionnat($championnat);

        $competition = new Competition();
        $competition->setNom('Nage Libre');
        
        $championnat->addCompetition($competition);

        $epreuve1 = new Epreuve();
        $epreuve1->setNom('50m Nage Libre');
        
        $epreuve2 = new Epreuve();
        $epreuve2->setNom('100m Nage Libre');
        
        $competition->addEpreuve($epreuve1);
        $competition->addEpreuve($epreuve2);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $sportId = $sport->getId();
        $this->entityManager->clear();

        $sportFromDb = $this->entityManager
            ->getRepository(Sport::class)
            ->find($sportId);

        $this->assertNotNull($sportFromDb);
        $this->assertEquals('Natation', $sportFromDb->getNom());
        $this->assertCount(1, $sportFromDb->getChampionnats());

        $championnatFromDb = $sportFromDb->getChampionnats()->first();
        $this->assertEquals('Championnats du Monde', $championnatFromDb->getNom());
        $this->assertCount(1, $championnatFromDb->getCompetitions());

        $competitionFromDb = $championnatFromDb->getCompetitions()->first();
        $this->assertEquals('Nage Libre', $competitionFromDb->getNom());
        $this->assertCount(2, $competitionFromDb->getEpreuves());

        foreach ($competitionFromDb->getEpreuves() as $epreuve) {
            $this->assertSame($competitionFromDb, $epreuve->getCompetition());
        }
    }

    public function testCascadeRemoveSportDeletesEverything(): void
    {
        $sport = new Sport();
        $sport->setNom('Cyclisme')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Tour de France');
        $sport->addChampionnat($championnat);

        $competition = new Competition();
        $competition->setNom('Étape de montagne');
        $championnat->addCompetition($competition);

        $epreuve = new Epreuve();
        $epreuve->setNom('Col du Galibier');
        $competition->addEpreuve($epreuve);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $championnatId = $championnat->getId();
        $competitionId = $competition->getId();
        $epreuveId = $epreuve->getId();

        $this->entityManager->remove($sport);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $this->assertNull($this->entityManager->getRepository(Championnat::class)->find($championnatId));
        $this->assertNull($this->entityManager->getRepository(Competition::class)->find($competitionId));
        $this->assertNull($this->entityManager->getRepository(Epreuve::class)->find($epreuveId));
    }

    public function testRemoveCompetitionDeletesEpreuves(): void
    {
        $sport = new Sport();
        $sport->setNom('Escrime')->setType('individuel');

        $championnat = new Championnat();
        $championnat->setNom('Championnats Européens');
        $sport->addChampionnat($championnat);

        $competition = new Competition();
        $competition->setNom('Fleuret');
        $championnat->addCompetition($competition);

        $epreuve = new Epreuve();
        $epreuve->setNom('Finale Individuelle');
        $competition->addEpreuve($epreuve);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $epreuveId = $epreuve->getId();

        $championnat->removeCompetition($competition);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $epreuveFromDb = $this->entityManager
            ->getRepository(Epreuve::class)
            ->find($epreuveId);

        $this->assertNull($epreuveFromDb);
    }
}
