<?php

namespace App\Tests\Behat;

use App\Entity\Epreuve;
use App\Entity\Competition;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;

class EpreuveContext implements Context
{
    private EntityManagerInterface $entityManager;
    private array $epreuves = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Given /^que je crée une épreuve "([^"]*)" pour la compétition "([^"]*)"$/
     * @When /^je crée une épreuve "([^"]*)" pour la compétition "([^"]*)"$/
     */
    public function jeCreéUneEpreuvePourLaCompetition(string $nomEpreuve, string $nomCompetition): void
    {
        $competition = $this->getCompetitionByName($nomCompetition);
        
        $epreuve = new Epreuve();
        $epreuve->setNom($nomEpreuve);
        $epreuve->setCompetition($competition);
        
        $this->entityManager->persist($epreuve);
        $this->entityManager->flush();
        
        $this->epreuves[$nomEpreuve] = $epreuve;
    }

    /**
     * @When /^je crée les épreuves suivantes pour la compétition "([^"]*)":$/
     */
    public function jeCreéLesEpreuvesSuivantesPourLaCompetition(string $nomCompetition, TableNode $table): void
    {
        $competition = $this->getCompetitionByName($nomCompetition);
        
        foreach ($table->getHash() as $row) {
            $epreuve = new Epreuve();
            $epreuve->setNom($row['nom']);
            $epreuve->setCompetition($competition);
            
            $this->entityManager->persist($epreuve);
            $this->epreuves[$row['nom']] = $epreuve;
        }
        
        $this->entityManager->flush();
    }

    /**
     * @When /^je supprime l'épreuve "([^"]*)"$/
     */
    public function jeSupprimeleEpreuve(string $nomEpreuve): void
    {
        $epreuve = $this->getEpreuveByName($nomEpreuve);
        
        $this->entityManager->remove($epreuve);
        $this->entityManager->flush();
    }

    /**
     * @When /^je retire l'épreuve "([^"]*)" de la compétition "([^"]*)"$/
     */
    public function jeRetireleEpreuveDeLaCompetition(string $nomEpreuve, string $nomCompetition): void
    {
        $competition = $this->getCompetitionByName($nomCompetition);
        $epreuve = $this->getEpreuveByName($nomEpreuve);
        
        $competition->removeEpreuve($epreuve);
        
        $this->entityManager->flush();
    }

    /**
     * @Then /^l'épreuve "([^"]*)" devrait exister$/
     */
    public function leEpreuveDevraitExister(string $nomEpreuve): void
    {
        $epreuve = $this->entityManager
            ->getRepository(Epreuve::class)
            ->findOneBy(['nom' => $nomEpreuve]);
        
        Assert::assertNotNull($epreuve, "L'épreuve '{$nomEpreuve}' devrait exister");
    }

    /**
     * @Then /^l'épreuve "([^"]*)" ne devrait pas exister$/
     */
    public function leEpreuveNeDevraitPasExister(string $nomEpreuve): void
    {
        $this->entityManager->clear();
        
        $epreuve = $this->entityManager
            ->getRepository(Epreuve::class)
            ->findOneBy(['nom' => $nomEpreuve]);
        
        Assert::assertNull($epreuve, "L'épreuve '{$nomEpreuve}' ne devrait pas exister");
    }

    /**
     * @Then /^l'épreuve "([^"]*)" devrait être associée à la compétition "([^"]*)"$/
     */
    public function leEpreuveDevraitEtreAssocieALaCompetition(string $nomEpreuve, string $nomCompetition): void
    {
        $epreuve = $this->getEpreuveByName($nomEpreuve);
        $competition = $this->getCompetitionByName($nomCompetition);
        
        Assert::assertSame(
            $competition->getId(),
            $epreuve->getCompetition()->getId(),
            "L'épreuve '{$nomEpreuve}' devrait être associée à la compétition '{$nomCompetition}'"
        );
    }

    /**
     * @Then /^la compétition "([^"]*)" devrait avoir (\d+) épreuves?$/
     */
    public function laCompetitionDevraitAvoirEpreuves(string $nomCompetition, int $count): void
    {
        $this->entityManager->clear();
        
        $competition = $this->entityManager
            ->getRepository(Competition::class)
            ->findOneBy(['nom' => $nomCompetition]);
        
        Assert::assertNotNull($competition);
        Assert::assertCount(
            $count,
            $competition->getEpreuves(),
            "La compétition '{$nomCompetition}' devrait avoir {$count} épreuve(s)"
        );
    }

    private function getEpreuveByName(string $nom): Epreuve
    {
        $epreuve = $this->entityManager
            ->getRepository(Epreuve::class)
            ->findOneBy(['nom' => $nom]);
        
        Assert::assertNotNull($epreuve, "L'épreuve '{$nom}' n'existe pas");
        
        return $epreuve;
    }

    private function getCompetitionByName(string $nom): Competition
    {
        $competition = $this->entityManager
            ->getRepository(Competition::class)
            ->findOneBy(['nom' => $nom]);
        
        Assert::assertNotNull($competition, "La compétition '{$nom}' n'existe pas");
        
        return $competition;
    }
}
