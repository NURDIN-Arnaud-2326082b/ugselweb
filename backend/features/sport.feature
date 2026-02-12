# language: fr
Fonctionnalité: Gestion des sports
  En tant qu'administrateur
  Je veux gérer les sports
  Afin d'organiser les championnats par discipline

  Contexte:
    Étant donné que la base de données est vide

  Scénario: Créer un sport individuel
    Quand je crée un sport "Tennis" de type "individuel"
    Alors le sport "Tennis" devrait exister
    Et le sport "Tennis" devrait être de type "individuel"

  Scénario: Créer un sport collectif
    Quand je crée un sport "Football" de type "collectif"
    Alors le sport "Football" devrait exister
    Et le sport "Football" devrait être de type "collectif"

  Scénario: Impossible de créer un sport avec un type invalide
    Quand j'essaie de créer un sport "Pétanque" de type "mixte"
    Alors une erreur devrait être levée

  Scénario: Lister les sports individuels
    Étant donné les sports suivants:
      | nom        | type       |
      | Tennis     | individuel |
      | Golf       | individuel |
      | Basketball | collectif  |
      | Football   | collectif  |
      | Natation   | individuel |
    Quand je liste les sports de type "individuel"
    Alors je devrais obtenir 3 sports
    Et la liste devrait contenir "Tennis"
    Et la liste devrait contenir "Golf"
    Et la liste devrait contenir "Natation"
    Mais la liste ne devrait pas contenir "Basketball"

  Scénario: Un sport peut avoir plusieurs championnats
    Étant donné un sport "Football" de type "collectif"
    Quand je crée les championnats suivants pour le sport "Football":
      | nom                     |
      | Ligue 1                 |
      | Coupe de France         |
      | Ligue des Champions     |
    Alors le sport "Football" devrait avoir 3 championnats
