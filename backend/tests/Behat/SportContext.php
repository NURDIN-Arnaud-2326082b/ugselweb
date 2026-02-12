<?php

namespace App\Tests\Behat;

use App\Entity\Sport;
use App\Repository\SportRepository;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;

class SportContext implements Context
{
    private EntityManagerInterface $entityManager;
    private SportRepository $sportRepository;
    private SharedExceptionStorage $exceptionStorage;
    private ?Sport $currentSport = null;
    private array $sports = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        SportRepository $sportRepository,
        SharedExceptionStorage $exceptionStorage
    ) {
        $this->entityManager = $entityManager;
        $this->sportRepository = $sportRepository;
        $this->exceptionStorage = $exceptionStorage;
    }

    /**
     * @When je crée un sport :nom de type :type
     * @Given un sport :nom de type :type
     */
    public function jeCreéUnSportDeType(string $nom, string $type): void
    {
        $sport = new Sport();
        $sport->setNom($nom);
        $sport->setType($type);

        $this->entityManager->persist($sport);
        $this->entityManager->flush();

        $this->currentSport = $sport;
        $this->sports[$nom] = $sport;
    }

    /**
     * @When j'essaie de créer un sport :nom de type :type
     */
    public function jessaieDeCreerUnSportDeType(string $nom, string $type): void
    {
        try {
            $this->jeCreéUnSportDeType($nom, $type);
            $this->exceptionStorage->setException(null);
        } catch (\Exception $e) {
            $this->exceptionStorage->setException($e);
        }
    }

    /**
     * @Given les sports suivants:
     */
    public function lesSportsSuivants(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $this->jeCreéUnSportDeType($row['nom'], $row['type']);
        }
    }

    /**
     * @When je liste les sports de type :type
     */
    public function jeListeLesSportsDeType(string $type): void
    {
        $this->sports = $this->sportRepository->findByType($type);
    }

    /**
     * @When je supprime le sport :nom
     */
    public function jeSupprimeLeSport(string $nom): void
    {
        $sport = $this->sportRepository->findOneBy(['nom' => $nom]);
        
        if ($sport) {
            $this->entityManager->remove($sport);
            $this->entityManager->flush();
        }
    }

    /**
     * @Then le sport :nom devrait exister
     */
    public function leSportDevraitExister(string $nom): void
    {
        $sport = $this->sportRepository->findOneBy(['nom' => $nom]);
        Assert::assertNotNull($sport, "Le sport '{$nom}' devrait exister");
    }

    /**
     * @Then le sport :nom ne devrait pas exister
     */
    public function leSportNeDevraitPasExister(string $nom): void
    {
        $sport = $this->sportRepository->findOneBy(['nom' => $nom]);
        Assert::assertNull($sport, "Le sport '{$nom}' ne devrait pas exister");
    }

    /**
     * @Then le sport :nom devrait être de type :type
     */
    public function leSportDevraitEtreDeType(string $nom, string $type): void
    {
        $sport = $this->sportRepository->findOneBy(['nom' => $nom]);
        Assert::assertNotNull($sport);
        Assert::assertEquals($type, $sport->getType());
    }

    /**
     * @Then une erreur devrait être levée
     */
    public function uneErreurDevraitEtreLevee(): void
    {
        $exception = $this->exceptionStorage->getException();
            
        if ($exception === null) {
            throw new \RuntimeException("Une exception devrait avoir été levée");
        }
    }

    /**
     * @Then je devrais obtenir :count sport(s)
     */
    public function jeDevraisObtenir(int $count): void
    {
        Assert::assertCount($count, $this->sports);
    }

    /**
     * @Then la liste devrait contenir :nom
     */
    public function laListeDevraitContenir(string $nom): void
    {
        $noms = array_map(fn($s) => $s->getNom(), $this->sports);
        Assert::assertContains($nom, $noms);
    }

    /**
     * @Then la liste ne devrait pas contenir :nom
     */
    public function laListeNeDevraitPasContenir(string $nom): void
    {
        $noms = array_map(fn($s) => $s->getNom(), $this->sports);
        Assert::assertNotContains($nom, $noms);
    }

    /**
     * @Then le sport :nom devrait avoir :count championnat(s)
     */
    public function leSportDevraitAvoirChampionnats(string $nom, int $count): void
    {
        $sport = $this->sportRepository->findOneBy(['nom' => $nom]);
        Assert::assertNotNull($sport);
        Assert::assertCount($count, $sport->getChampionnats());
    }

    public function getSportByName(string $nom): ?Sport
    {
        if (isset($this->sports[$nom]) && $this->sports[$nom] instanceof Sport) {
            return $this->sports[$nom];
        }
        
        return $this->sportRepository->findOneBy(['nom' => $nom]);
    }
}
