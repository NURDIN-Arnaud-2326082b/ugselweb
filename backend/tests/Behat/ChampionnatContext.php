<?php

namespace App\Tests\Behat;

use App\Entity\Championnat;
use App\Repository\ChampionnatRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;

class ChampionnatContext implements Context
{
    private EntityManagerInterface $entityManager;
    private ChampionnatRepository $championnatRepository;
    private SportContext $sportContext;
    private array $championnats = [];
    private ?\Exception $lastException = null;

    public function __construct(
        EntityManagerInterface $entityManager,
        ChampionnatRepository $championnatRepository,
        SportContext $sportContext
    ) {
        $this->entityManager = $entityManager;
        $this->championnatRepository = $championnatRepository;
        $this->sportContext = $sportContext;
    }

    /**
     * @When je crée un championnat :nom pour le sport :sportNom
     * @Given un championnat :nom pour le sport :sportNom
     */
    public function jeCreéUnChampionnatPourLeSport(string $nom, string $sportNom): void
    {
        $sport = $this->sportContext->getSportByName($sportNom);
        Assert::assertNotNull($sport, "Le sport '{$sportNom}' doit exister");

        $championnat = new Championnat();
        $championnat->setNom($nom);
        $championnat->setSport($sport);

        $this->entityManager->persist($championnat);
        $this->entityManager->flush();

        $this->championnats[$nom] = $championnat;
    }

    /**
     * @When je crée les championnats suivants pour le sport :sportNom:
     * @Given les championnats suivants pour le sport :sportNom:
     */
    public function jeCreéLesChampionnatsSuivantsPourLeSport(string $sportNom, TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $this->jeCreéUnChampionnatPourLeSport($row['nom'], $sportNom);
        }
    }

    /**
     * @When j'essaie de créer un championnat :nom sans sport
     */
    public function jessaieDeCreerUnChampionnatSansSport(string $nom): void
    {
        try {
            $championnat = new Championnat();
            $championnat->setNom($nom);
            
            $this->entityManager->persist($championnat);
            $this->entityManager->flush();
            
            $this->lastException = null;
        } catch (\Exception $e) {
            $this->lastException = $e;
        }
    }

    /**
     * @When je supprime le championnat :nom
     */
    public function jeSupprimeleChampionnat(string $nom): void
    {
        $championnat = $this->championnatRepository->findOneBy(['nom' => $nom]);
        
        if ($championnat) {
            $this->entityManager->remove($championnat);
            $this->entityManager->flush();
        }
    }

    /**
     * @When je retire le championnat :nom du sport :sportNom
     */
    public function jeRetireLeChampionnatDuSport(string $nom, string $sportNom): void
    {
        $sport = $this->sportContext->getSportByName($sportNom);
        $championnat = $this->championnatRepository->findOneBy(['nom' => $nom]);
        
        if ($sport && $championnat) {
            $sport->removeChampionnat($championnat);
            $this->entityManager->flush();
        }
    }

    /**
     * @Then le championnat :nom devrait exister
     */
    public function leChampionnatDevraitExister(string $nom): void
    {
        $championnat = $this->championnatRepository->findOneBy(['nom' => $nom]);
        Assert::assertNotNull($championnat, "Le championnat '{$nom}' devrait exister");
    }

    /**
     * @Then le championnat :nom ne devrait pas exister
     */
    public function leChampionnatNeDevraitPasExister(string $nom): void
    {
        $championnat = $this->championnatRepository->findOneBy(['nom' => $nom]);
        Assert::assertNull($championnat, "Le championnat '{$nom}' ne devrait pas exister");
    }

    /**
     * @Then le championnat :nom devrait être associé au sport :sportNom
     * @Then le championnat :nom devrait être lié au sport :sportNom
     */
    public function leChampionnatDevraitEtreAssocieAuSport(string $nom, string $sportNom): void
    {
        $championnat = $this->championnatRepository->findOneBy(['nom' => $nom]);
        Assert::assertNotNull($championnat);
        Assert::assertNotNull($championnat->getSport());
        Assert::assertEquals($sportNom, $championnat->getSport()->getNom());
    }

    /**
     * @Then les championnats du sport :sportNom ne devraient pas inclure :nom
     */
    public function lesChampionnatsDuSportNeDevraientPasInclure(string $sportNom, string $nom): void
    {
        $sport = $this->sportContext->getSportByName($sportNom);
        Assert::assertNotNull($sport);
        
        $noms = array_map(
            fn($c) => $c->getNom(),
            $sport->getChampionnats()->toArray()
        );
        
        Assert::assertNotContains($nom, $noms);
    }

    /**
     * @Then le championnat :nom devrait avoir :count compétition(s)
     */
    public function leChampionnatDevraitAvoirCompetitions(string $nom, int $count): void
    {
        $championnat = $this->championnatRepository->findOneBy(['nom' => $nom]);
        Assert::assertNotNull($championnat);
        Assert::assertCount($count, $championnat->getCompetitions());
    }

    public function getChampionnatByName(string $nom): ?Championnat
    {
        if (isset($this->championnats[$nom]) && $this->championnats[$nom] instanceof Championnat) {
            return $this->championnats[$nom];
        }
        
        return $this->championnatRepository->findOneBy(['nom' => $nom]);
    }
}
