<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Epreuve;
use App\Entity\Competition;
use PHPUnit\Framework\TestCase;

class EpreuveTest extends TestCase
{
    private Epreuve $epreuve;

    protected function setUp(): void
    {
        $this->epreuve = new Epreuve();
    }

    public function testGetId(): void
    {
        $this->assertNull($this->epreuve->getId());
    }

    public function testSetAndGetNom(): void
    {
        $nom = 'Finale 100m';
        $result = $this->epreuve->setNom($nom);

        $this->assertSame($nom, $this->epreuve->getNom());
        $this->assertSame($this->epreuve, $result, 'setNom should return the entity for fluent interface');
    }

    public function testSetAndGetCompetition(): void
    {
        $competition = new Competition();
        $competition->setNom('Championnats de France');

        $result = $this->epreuve->setCompetition($competition);

        $this->assertSame($competition, $this->epreuve->getCompetition());
        $this->assertSame($this->epreuve, $result, 'setCompetition should return the entity for fluent interface');
    }

    public function testSetCompetitionToNull(): void
    {
        $competition = new Competition();
        $this->epreuve->setCompetition($competition);

        $this->epreuve->setCompetition(null);

        $this->assertNull($this->epreuve->getCompetition());
    }

    public function testNewEpreuveHasNoCompetition(): void
    {
        $epreuve = new Epreuve();

        $this->assertNull($epreuve->getCompetition());
        $this->assertNull($epreuve->getNom());
    }
}
