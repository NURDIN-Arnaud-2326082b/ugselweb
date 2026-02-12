<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Competition;
use App\Entity\Championnat;
use App\Entity\Epreuve;
use PHPUnit\Framework\TestCase;

class CompetitionTest extends TestCase
{
    private Competition $competition;

    protected function setUp(): void
    {
        $this->competition = new Competition();
    }

    public function testNewCompetitionIsEmpty(): void
    {
        $this->assertNull($this->competition->getId());
        $this->assertNull($this->competition->getNom());
        $this->assertNull($this->competition->getChampionnat());
        $this->assertCount(0, $this->competition->getEpreuves());
    }

    public function testSetAndGetNom(): void
    {
        $nom = 'Finale Régionale';
        $this->competition->setNom($nom);
        
        $this->assertEquals($nom, $this->competition->getNom());
    }

    public function testSetAndGetChampionnat(): void
    {
        $championnat = new Championnat();
        $championnat->setNom('Championnat de France');
        
        $this->competition->setChampionnat($championnat);
        
        $this->assertSame($championnat, $this->competition->getChampionnat());
    }

    public function testSetChampionnatToNull(): void
    {
        $championnat = new Championnat();
        $this->competition->setChampionnat($championnat);
        $this->competition->setChampionnat(null);
        
        $this->assertNull($this->competition->getChampionnat());
    }

    public function testSetNomReturnsInstance(): void
    {
        $result = $this->competition->setNom('Demi-finale');
        
        $this->assertInstanceOf(Competition::class, $result);
        $this->assertSame($this->competition, $result);
    }

    public function testSetChampionnatReturnsInstance(): void
    {
        $championnat = new Championnat();
        $result = $this->competition->setChampionnat($championnat);
        
        $this->assertInstanceOf(Competition::class, $result);
        $this->assertSame($this->competition, $result);
    }

    public function testAddEpreuve(): void
    {
        $epreuve = new Epreuve();
        $epreuve->setNom('100m');
        
        $this->competition->addEpreuve($epreuve);
        
        $this->assertCount(1, $this->competition->getEpreuves());
        $this->assertTrue($this->competition->getEpreuves()->contains($epreuve));
        $this->assertSame($this->competition, $epreuve->getCompetition());
    }

    public function testAddSameEpreuveTwiceOnlyAddsOnce(): void
    {
        $epreuve = new Epreuve();
        $epreuve->setNom('200m');
        
        $this->competition->addEpreuve($epreuve);
        $this->competition->addEpreuve($epreuve);
        
        $this->assertCount(1, $this->competition->getEpreuves());
    }

    public function testAddMultipleEpreuves(): void
    {
        $epreuve1 = new Epreuve();
        $epreuve1->setNom('100m');
        
        $epreuve2 = new Epreuve();
        $epreuve2->setNom('200m');
        
        $epreuve3 = new Epreuve();
        $epreuve3->setNom('400m');
        
        $this->competition->addEpreuve($epreuve1);
        $this->competition->addEpreuve($epreuve2);
        $this->competition->addEpreuve($epreuve3);
        
        $this->assertCount(3, $this->competition->getEpreuves());
    }

    public function testRemoveEpreuve(): void
    {
        $epreuve = new Epreuve();
        $epreuve->setNom('100m');
        
        $this->competition->addEpreuve($epreuve);
        $this->assertCount(1, $this->competition->getEpreuves());
        
        $this->competition->removeEpreuve($epreuve);
        
        $this->assertCount(0, $this->competition->getEpreuves());
        $this->assertNull($epreuve->getCompetition());
    }

    public function testRemoveEpreuveThatDoesNotExist(): void
    {
        $epreuve = new Epreuve();
        $epreuve->setNom('100m');
        
        $this->competition->removeEpreuve($epreuve);
        
        $this->assertCount(0, $this->competition->getEpreuves());
    }

    public function testAddEpreuveReturnsInstance(): void
    {
        $epreuve = new Epreuve();
        $result = $this->competition->addEpreuve($epreuve);
        
        $this->assertInstanceOf(Competition::class, $result);
        $this->assertSame($this->competition, $result);
    }

    public function testRemoveEpreuveReturnsInstance(): void
    {
        $epreuve = new Epreuve();
        $result = $this->competition->removeEpreuve($epreuve);
        
        $this->assertInstanceOf(Competition::class, $result);
        $this->assertSame($this->competition, $result);
    }
}
