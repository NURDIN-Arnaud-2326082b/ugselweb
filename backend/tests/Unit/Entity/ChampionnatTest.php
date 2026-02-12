<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Championnat;
use App\Entity\Sport;
use App\Entity\Competition;
use PHPUnit\Framework\TestCase;

class ChampionnatTest extends TestCase
{
    private Championnat $championnat;

    protected function setUp(): void
    {
        $this->championnat = new Championnat();
    }

    public function testNewChampionnatIsEmpty(): void
    {
        $this->assertNull($this->championnat->getId());
        $this->assertNull($this->championnat->getNom());
        $this->assertNull($this->championnat->getSport());
        $this->assertCount(0, $this->championnat->getCompetitions());
    }

    public function testSetAndGetNom(): void
    {
        $nom = 'Championnat National';
        $this->championnat->setNom($nom);
        
        $this->assertEquals($nom, $this->championnat->getNom());
    }

    public function testSetAndGetSport(): void
    {
        $sport = new Sport();
        $sport->setNom('Tennis');
        
        $this->championnat->setSport($sport);
        
        $this->assertSame($sport, $this->championnat->getSport());
    }

    public function testSetSportToNull(): void
    {
        $sport = new Sport();
        $this->championnat->setSport($sport);
        $this->championnat->setSport(null);
        
        $this->assertNull($this->championnat->getSport());
    }

    public function testAddCompetition(): void
    {
        $competition = new Competition();
        $competition->setNom('Finale');
        
        $this->championnat->addCompetition($competition);
        
        $this->assertCount(1, $this->championnat->getCompetitions());
        $this->assertTrue($this->championnat->getCompetitions()->contains($competition));
        $this->assertSame($this->championnat, $competition->getChampionnat());
    }

    public function testAddSameCompetitionTwiceOnlyAddsOnce(): void
    {
        $competition = new Competition();
        $competition->setNom('Demi-finale');
        
        $this->championnat->addCompetition($competition);
        $this->championnat->addCompetition($competition);
        
        $this->assertCount(1, $this->championnat->getCompetitions());
    }

    public function testAddMultipleCompetitions(): void
    {
        $competition1 = new Competition();
        $competition1->setNom('Quarts de finale');
        
        $competition2 = new Competition();
        $competition2->setNom('Demi-finales');
        
        $competition3 = new Competition();
        $competition3->setNom('Finale');
        
        $this->championnat->addCompetition($competition1);
        $this->championnat->addCompetition($competition2);
        $this->championnat->addCompetition($competition3);
        
        $this->assertCount(3, $this->championnat->getCompetitions());
    }

    public function testRemoveCompetition(): void
    {
        $competition = new Competition();
        $competition->setNom('Finale');
        
        $this->championnat->addCompetition($competition);
        $this->assertCount(1, $this->championnat->getCompetitions());
        
        $this->championnat->removeCompetition($competition);
        
        $this->assertCount(0, $this->championnat->getCompetitions());
        $this->assertNull($competition->getChampionnat());
    }

    public function testRemoveCompetitionThatDoesNotExist(): void
    {
        $competition = new Competition();
        $competition->setNom('Finale');
        
        // N'ajoute pas la compétition, essaie juste de la retirer
        $this->championnat->removeCompetition($competition);
        
        $this->assertCount(0, $this->championnat->getCompetitions());
    }

    public function testSetNomReturnsInstance(): void
    {
        $result = $this->championnat->setNom('Coupe du Monde');
        
        $this->assertInstanceOf(Championnat::class, $result);
        $this->assertSame($this->championnat, $result);
    }

    public function testSetSportReturnsInstance(): void
    {
        $sport = new Sport();
        $result = $this->championnat->setSport($sport);
        
        $this->assertInstanceOf(Championnat::class, $result);
        $this->assertSame($this->championnat, $result);
    }

    public function testAddCompetitionReturnsInstance(): void
    {
        $competition = new Competition();
        $result = $this->championnat->addCompetition($competition);
        
        $this->assertInstanceOf(Championnat::class, $result);
        $this->assertSame($this->championnat, $result);
    }

    public function testRemoveCompetitionReturnsInstance(): void
    {
        $competition = new Competition();
        $result = $this->championnat->removeCompetition($competition);
        
        $this->assertInstanceOf(Championnat::class, $result);
        $this->assertSame($this->championnat, $result);
    }
}
