# language: fr
Fonctionnalité: Cascade de suppression et relations
  En tant qu'administrateur
  Je veux que la suppression en cascade fonctionne correctement
  Afin de maintenir l'intégrité des données

  Contexte:
    Étant donné que la base de données est vide

  Scénario: Supprimer un sport supprime tous ses championnats
    Étant donné un sport "Rugby" de type "collectif"
    Et les championnats suivants pour le sport "Rugby":
      | nom                     |
      | Top 14                  |
      | Coupe d'Europe          |
      | Championnat du Monde    |
    Quand je supprime le sport "Rugby"
    Alors le sport "Rugby" ne devrait pas exister
    Et le championnat "Top 14" ne devrait pas exister
    Et le championnat "Coupe d'Europe" ne devrait pas exister

  Scénario: Supprimer un sport supprime championnats et compétitions en cascade
    Étant donné un sport "Cyclisme" de type "individuel"
    Et un championnat "Tour de France" pour le sport "Cyclisme"
    Et les compétitions suivantes pour le championnat "Tour de France":
      | nom                  |
      | Contre-la-montre     |
      | Étape de montagne    |
      | Étape de plaine      |
    Quand je supprime le sport "Cyclisme"
    Alors le sport "Cyclisme" ne devrait pas exister
    Et le championnat "Tour de France" ne devrait pas exister
    Et la compétition "Contre-la-montre" ne devrait pas exister
    Et la compétition "Étape de montagne" ne devrait pas exister

  Scénario: Supprimer un championnat supprime toutes ses compétitions
    Étant donné un sport "Escrime" de type "individuel"
    Et un championnat "Championnats de France" pour le sport "Escrime"
    Et les compétitions suivantes pour le championnat "Championnats de France":
      | nom           |
      | Fleuret       |
      | Épée          |
      | Sabre         |
    Quand je supprime le championnat "Championnats de France"
    Alors le championnat "Championnats de France" ne devrait pas exister
    Et la compétition "Fleuret" ne devrait pas exister
    Et la compétition "Épée" ne devrait pas exister
    Mais le sport "Escrime" devrait exister

  Scénario: Retirer un championnat d'un sport supprime ses compétitions (orphanRemoval)
    Étant donné un sport "Boxe" de type "individuel"
    Et un championnat "Golden Gloves" pour le sport "Boxe"
    Et les compétitions suivantes pour le championnat "Golden Gloves":
      | nom          |
      | Poids mouche |
      | Poids lourd  |
    Quand je retire le championnat "Golden Gloves" du sport "Boxe"
    Alors la compétition "Poids mouche" ne devrait pas exister
    Et la compétition "Poids lourd" ne devrait pas exister

  Scénario: Hiérarchie complète Sport → Championnat → Compétition
    Étant donné un sport "Handball" de type "collectif"
    Et un championnat "Ligue des Champions" pour le sport "Handball"
    Et les compétitions suivantes pour le championnat "Ligue des Champions":
      | nom                    |
      | Phase de groupes       |
      | Quarts de finale       |
      | Demi-finales           |
      | Finale                 |
    Alors le sport "Handball" devrait avoir 1 championnat
    Et le championnat "Ligue des Champions" devrait avoir 4 compétitions
    Et chaque compétition devrait être liée au championnat "Ligue des Champions"
    Et le championnat "Ligue des Champions" devrait être lié au sport "Handball"

  Scénario: Hiérarchie complète Sport → Championnat → Compétition → Épreuve
    Étant donné un sport "Athlétisme" de type "individuel"
    Et un championnat "Championnats du Monde" pour le sport "Athlétisme"
    Et une compétition "Sprint" pour le championnat "Championnats du Monde"
    Et les épreuves suivantes pour la compétition "Sprint":
      | nom   |
      | 100m  |
      | 200m  |
      | 400m  |
    Alors le sport "Athlétisme" devrait avoir 1 championnat
    Et le championnat "Championnats du Monde" devrait avoir 1 compétition
    Et la compétition "Sprint" devrait avoir 3 épreuves

  Scénario: Cascade complète - supprimer un sport supprime tout jusqu'aux épreuves
    Étant donné un sport "Natation" de type "individuel"
    Et un championnat "Jeux Olympiques" pour le sport "Natation"
    Et une compétition "Nage Libre" pour le championnat "Jeux Olympiques"
    Et les épreuves suivantes pour la compétition "Nage Libre":
      | nom               |
      | 50m Nage Libre    |
      | 100m Nage Libre   |
    Quand je supprime le sport "Natation"
    Alors le sport "Natation" ne devrait pas exister
    Et le championnat "Jeux Olympiques" ne devrait pas exister
    Et la compétition "Nage Libre" ne devrait pas exister
    Et l'épreuve "50m Nage Libre" ne devrait pas exister
    Et l'épreuve "100m Nage Libre" ne devrait pas exister

  Scénario: Supprimer un championnat supprime compétitions et épreuves
    Étant donné un sport "Gymnastique" de type "individuel"
    Et un championnat "Coupe du Monde" pour le sport "Gymnastique"
    Et une compétition "Artistique" pour le championnat "Coupe du Monde"
    Et les épreuves suivantes pour la compétition "Artistique":
      | nom                  |
      | Barres parallèles    |
      | Anneaux              |
    Quand je supprime le championnat "Coupe du Monde"
    Alors le championnat "Coupe du Monde" ne devrait pas exister
    Et la compétition "Artistique" ne devrait pas exister
    Et l'épreuve "Barres parallèles" ne devrait pas exister
    Et l'épreuve "Anneaux" ne devrait pas exister
    Mais le sport "Gymnastique" devrait exister

  Scénario: Supprimer une compétition supprime ses épreuves
    Étant donné un sport "Judo" de type "individuel"
    Et un championnat "Open de Paris" pour le sport "Judo"
    Et une compétition "-60kg" pour le championnat "Open de Paris"
    Et les épreuves suivantes pour la compétition "-60kg":
      | nom              |
      | Qualification    |
      | Finale           |
    Quand je supprime la compétition "-60kg"
    Alors la compétition "-60kg" ne devrait pas exister
    Et l'épreuve "Qualification" ne devrait pas exister
    Et l'épreuve "Finale" ne devrait pas exister
    Mais le championnat "Open de Paris" devrait exister
    Et le sport "Judo" devrait exister
