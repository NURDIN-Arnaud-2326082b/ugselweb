# language: fr
Fonctionnalité: Gestion des épreuves
  En tant qu'administrateur
  Je veux gérer les épreuves des compétitions
  Pour organiser les événements sportifs

  Contexte:
    Étant donné que la base de données est vide
    Et que je crée un sport "Athlétisme" de type "individuel"
    Et que je crée un championnat "Championnats de France" pour le sport "Athlétisme"
    Et que je crée une compétition "Sprint" pour le championnat "Championnats de France"

  Scénario: Créer une épreuve pour une compétition
    Quand je crée une épreuve "100m" pour la compétition "Sprint"
    Alors l'épreuve "100m" devrait exister
    Et l'épreuve "100m" devrait être associée à la compétition "Sprint"

  Scénario: Créer plusieurs épreuves pour une compétition
    Quand je crée les épreuves suivantes pour la compétition "Sprint":
      | nom   |
      | 100m  |
      | 200m  |
      | 400m  |
    Alors la compétition "Sprint" devrait avoir 3 épreuves
    Et l'épreuve "100m" devrait exister
    Et l'épreuve "200m" devrait exister
    Et l'épreuve "400m" devrait exister

  Scénario: Supprimer une épreuve
    Étant donné que je crée une épreuve "100m" pour la compétition "Sprint"
    Quand je supprime l'épreuve "100m"
    Alors l'épreuve "100m" ne devrait pas exister

  Scénario: Retirer une épreuve d'une compétition
    Étant donné que je crée une épreuve "100m" pour la compétition "Sprint"
    Et que je crée une épreuve "200m" pour la compétition "Sprint"
    Quand je retire l'épreuve "100m" de la compétition "Sprint"
    Alors l'épreuve "100m" ne devrait pas exister
    Et l'épreuve "200m" devrait exister
    Et la compétition "Sprint" devrait avoir 1 épreuve

  Scénario: Suppression en cascade - supprimer une compétition supprime ses épreuves
    Étant donné que je crée les épreuves suivantes pour la compétition "Sprint":
      | nom   |
      | 100m  |
      | 200m  |
    Quand je supprime la compétition "Sprint"
    Alors la compétition "Sprint" ne devrait pas exister
    Et l'épreuve "100m" ne devrait pas exister
    Et l'épreuve "200m" ne devrait pas exister

  Scénario: Suppression en cascade complète - supprimer un sport supprime tout
    Étant donné que je crée une épreuve "100m" pour la compétition "Sprint"
    Quand je supprime le sport "Athlétisme"
    Alors le sport "Athlétisme" ne devrait pas exister
    Et le championnat "Championnats de France" ne devrait pas exister
    Et la compétition "Sprint" ne devrait pas exister
    Et l'épreuve "100m" ne devrait pas exister
