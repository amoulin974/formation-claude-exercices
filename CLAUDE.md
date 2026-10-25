# Exercices de la formation « Développer avec Claude »

Deux implémentations du même domaine (gestion de tournoi) : `cpp/` et `php/`.
Le comportement doit rester identique dans les deux langages.

## Commandes

- Tous les tests : `make test`
- C++ : `make test-cpp` (CMake + Ninja, GoogleTest)
- C++ avec sanitizers : `cmake -S cpp -B cpp/build-asan -DTOURNOI_SANITIZERS=ON`
- PHP : `make test-php` (PHPUnit 11)
- Agent minimal : `make test-agent` (unittest, sans clé d'API)
- Classement C++ : `cpp/build/tournoi-cli classement donnees/resultats-2026.csv`
- Classement PHP : `php php/bin/tournoi.php classement donnees/resultats-2026.csv`

## Organisation

- `cpp/` et `php/` : deux implémentations du calendrier (`calendrier`) et du classement (`classement`).
- `donnees/` : résultats d'une saison au format CSV, pour essayer l'outil.
- `agent/` : agent minimal en Python construit sur l'API Claude (séance 3).

## Conventions

- Identifiants et commentaires en français (`genererRoundRobin`, `Journee`, `exempte`).
- C++20, namespace `tournoi`, en-têtes dans `cpp/include/tournoi/`.
- PHP 8.3 avec `declare(strict_types=1)`, namespace `Tournoi`, classes `final`.
- Toute modification de comportement s'accompagne d'un test, dans les deux langages.
- Lancer `make test` avant de proposer un commit.
