<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Sport;
use App\Entity\Championnat;
use PHPUnit\Framework\TestCase;

class SportTest extends TestCase
{
    private Sport $sport;

    protected function setUp(): void
    {
        $this->sport = new Sport();
    }

    public function testNewSportIsEmpty(): void
    {
        $this->assertNull($this->sport->getId());
        $this->assertNull($this->sport->getNom());
        $this->assertNull($this->sport->getType());
        $this->assertCount(0, $this->sport->getChampionnats());
    }

    public function testSetAndGetNom(): void
    {
        $nom = 'Football';
        $this->sport->setNom($nom);
        
        $this->assertEquals($nom, $this->sport->getNom());
    }

    public function testSetAndGetTypeIndividuel(): void
    {
        $this->sport->setType('individuel');
        
        $this->assertEquals('individuel', $this->sport->getType());
    }

    public function testSetAndGetTypeCollectif(): void
    {
        $this->sport->setType('collectif');
        
        $this->assertEquals('collectif', $this->sport->getType());
    }

    public function testSetTypeInvalidThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le type doit être 'individuel' ou 'collectif'");
        
        $this->sport->setType('invalide');
    }

    public function testAddChampionnat(): void
    {
        $championnat = new Championnat();
        $championnat->setNom('Championnat National');
        
        $this->sport->addChampionnat($championnat);
        
        $this->assertCount(1, $this->sport->getChampionnats());
        $this->assertTrue($this->sport->getChampionnats()->contains($championnat));
        $this->assertSame($this->sport, $championnat->getSport());
    }

    public function testAddSameChampionnatTwiceOnlyAddsOnce(): void
    {
        $championnat = new Championnat();
        $championnat->setNom('Championnat');
        
        $this->sport->addChampionnat($championnat);
        $this->sport->addChampionnat($championnat);
        
        $this->assertCount(1, $this->sport->getChampionnats());
    }

    public function testRemoveChampionnat(): void
    {
        $championnat = new Championnat();
        $championnat->setNom('Championnat');
        
        $this->sport->addChampionnat($championnat);
        $this->assertCount(1, $this->sport->getChampionnats());
        
        $this->sport->removeChampionnat($championnat);
        
        $this->assertCount(0, $this->sport->getChampionnats());
        $this->assertNull($championnat->getSport());
    }

    public function testRemoveChampionnatThatDoesNotExist(): void
    {
        $championnat = new Championnat();
        $championnat->setNom('Championnat');
        
        // N'ajoute pas le championnat, essaie juste de le retirer
        $this->sport->removeChampionnat($championnat);
        
        $this->assertCount(0, $this->sport->getChampionnats());
    }

    public function testSetNomReturnsInstance(): void
    {
        $result = $this->sport->setNom('Basketball');
        
        $this->assertInstanceOf(Sport::class, $result);
        $this->assertSame($this->sport, $result);
    }

    public function testSetTypeReturnsInstance(): void
    {
        $result = $this->sport->setType('collectif');
        
        $this->assertInstanceOf(Sport::class, $result);
        $this->assertSame($this->sport, $result);
    }

    public function testAddChampionnatReturnsInstance(): void
    {
        $championnat = new Championnat();
        $result = $this->sport->addChampionnat($championnat);
        
        $this->assertInstanceOf(Sport::class, $result);
        $this->assertSame($this->sport, $result);
    }

    public function testRemoveChampionnatReturnsInstance(): void
    {
        $championnat = new Championnat();
        $result = $this->sport->removeChampionnat($championnat);
        
        $this->assertInstanceOf(Sport::class, $result);
        $this->assertSame($this->sport, $result);
    }
}
