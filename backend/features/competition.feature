# language: fr
Fonctionnalité: Gestion des compétitions
  En tant qu'administrateur
  Je veux gérer les compétitions associées aux championnats
  Afin d'organiser les différentes phases d'un championnat

  Contexte:
    Étant donné que la base de données est vide

  Scénario: Créer une compétition pour un championnat
    Étant donné un sport "Football" de type "collectif"
    Et un championnat "Ligue 1" pour le sport "Football"
    Quand je crée une compétition "Journée 1" pour le championnat "Ligue 1"
    Alors la compétition "Journée 1" devrait exister
    Et la compétition "Journée 1" devrait être associée au championnat "Ligue 1"

  Scénario: Supprimer une compétition d'un championnat
    Étant donné un sport "Tennis" de type "individuel"
    Et un championnat "Wimbledon" pour le sport "Tennis"
    Et les compétitions suivantes pour le championnat "Wimbledon":
      | nom                |
      | Simple Messieurs   |
      | Simple Dames       |
      | Double Mixte       |
    Quand je supprime la compétition "Double Mixte"
    Alors le championnat "Wimbledon" devrait avoir 2 compétitions

  Scénario: Impossible de créer une compétition sans championnat
    Quand j'essaie de créer une compétition "Finale" sans championnat
    Alors une erreur devrait être levée
