# language: fr
Fonctionnalité: Gestion des championnats
  En tant qu'administrateur
  Je veux gérer les championnats associés aux sports
  Afin d'organiser les événements sportifs

  Contexte:
    Étant donné que la base de données est vide

  Scénario: Créer un championnat pour un sport
    Étant donné un sport "Football" de type "collectif"
    Quand je crée un championnat "Ligue 1" pour le sport "Football"
    Alors le championnat "Ligue 1" devrait exister
    Et le championnat "Ligue 1" devrait être associé au sport "Football"

  Scénario: Un championnat peut avoir plusieurs compétitions
    Étant donné un sport "Tennis" de type "individuel"
    Et un championnat "Roland Garros" pour le sport "Tennis"
    Quand je crée les compétitions suivantes pour le championnat "Roland Garros":
      | nom                |
      | Simple Messieurs   |
      | Simple Dames       |
      | Double Messieurs   |
      | Double Dames       |
    Alors le championnat "Roland Garros" devrait avoir 4 compétitions

  Scénario: Supprimer un championnat d'un sport
    Étant donné un sport "Basketball" de type "collectif"
    Et les championnats suivants pour le sport "Basketball":
      | nom          |
      | NBA          |
      | Euroleague   |
    Quand je supprime le championnat "NBA"
    Alors le sport "Basketball" devrait avoir 1 championnat
    Et les championnats du sport "Basketball" ne devraient pas inclure "NBA"

  Scénario: Impossible de créer un championnat sans sport
    Quand j'essaie de créer un championnat "Coupe du Monde" sans sport
    Alors une erreur devrait être levée
