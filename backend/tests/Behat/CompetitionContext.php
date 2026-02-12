<?php

namespace App\Tests\Behat;

use App\Entity\Competition;
use App\Repository\CompetitionRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;

class CompetitionContext implements Context
{
    private EntityManagerInterface $entityManager;
    private CompetitionRepository $competitionRepository;
    private ChampionnatContext $championnatContext;
    private SharedExceptionStorage $exceptionStorage;
    private array $competitions = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        CompetitionRepository $competitionRepository,
        ChampionnatContext $championnatContext,
        SharedExceptionStorage $exceptionStorage
    ) {
        $this->entityManager = $entityManager;
        $this->competitionRepository = $competitionRepository;
        $this->championnatContext = $championnatContext;
        $this->exceptionStorage = $exceptionStorage;
    }

    /**
     * @When je crée une compétition :nom pour le championnat :championnatNom
     * @Given une compétition :nom pour le championnat :championnatNom
     */
    public function jeCreéUneCompetitionPourLeChampionnat(string $nom, string $championnatNom): void
    {
        $championnat = $this->championnatContext->getChampionnatByName($championnatNom);
        Assert::assertNotNull($championnat, "Le championnat '{$championnatNom}' doit exister");

        $competition = new Competition();
        $competition->setNom($nom);
        $championnat->addCompetition($competition);

        $this->entityManager->persist($championnat);
        $this->entityManager->flush();

        $this->competitions[$nom] = $competition;
    }

    /**
     * @When je crée les compétitions suivantes pour le championnat :championnatNom:
     * @Given les compétitions suivantes pour le championnat :championnatNom:
     */
    public function jeCreéLesCompetitionsSuivantesPourLeChampionnat(string $championnatNom, TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $this->jeCreéUneCompetitionPourLeChampionnat($row['nom'], $championnatNom);
        }
    }

    /**
     * @When j'essaie de créer une compétition :nom sans championnat
     */
    public function jessaieDeCreerUneCompetitionSansChampionnat(string $nom): void
    {
        try {
            $competition = new Competition();
            $competition->setNom($nom);
            
            $this->entityManager->persist($competition);
            $this->entityManager->flush();
            
            $this->exceptionStorage->setException(null);
        } catch (\Exception $e) {
            $this->exceptionStorage->setException($e);
        }
    }

    /**
     * @When je supprime la compétition :nom
     */
    public function jeSupprimeLaCompetition(string $nom): void
    {
        $competition = $this->competitionRepository->findOneBy(['nom' => $nom]);
        
        if ($competition) {
            $this->entityManager->remove($competition);
            $this->entityManager->flush();
        }
    }

    /**
     * @Then la compétition :nom devrait exister
     */
    public function laCompetitionDevraitExister(string $nom): void
    {
        $competition = $this->competitionRepository->findOneBy(['nom' => $nom]);
        Assert::assertNotNull($competition, "La compétition '{$nom}' devrait exister");
    }

    /**
     * @Then la compétition :nom ne devrait pas exister
     */
    public function laCompetitionNeDevraitPasExister(string $nom): void
    {
        $competition = $this->competitionRepository->findOneBy(['nom' => $nom]);
        Assert::assertNull($competition, "La compétition '{$nom}' ne devrait pas exister");
    }

    /**
     * @Then la compétition :nom devrait être associée au championnat :championnatNom
     */
    public function laCompetitionDevraitEtreAssocieeAuChampionnat(string $nom, string $championnatNom): void
    {
        $competition = $this->competitionRepository->findOneBy(['nom' => $nom]);
        Assert::assertNotNull($competition);
        Assert::assertNotNull($competition->getChampionnat());
        Assert::assertEquals($championnatNom, $competition->getChampionnat()->getNom());
    }

    /**
     * @Then chaque compétition devrait être liée au championnat :championnatNom
     */
    public function chaqueCompetitionDevraitEtreLieeAuChampionnat(string $championnatNom): void
    {
        $championnat = $this->championnatContext->getChampionnatByName($championnatNom);
        Assert::assertNotNull($championnat);
        
        foreach ($championnat->getCompetitions() as $competition) {
            Assert::assertSame($championnat, $competition->getChampionnat());
        }
    }
}
