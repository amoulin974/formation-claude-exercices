# Exercices de la formation « Développer avec Claude »

Deux implémentations du même domaine (gestion de tournoi) : `cpp/` et `php/`.
Le comportement doit rester identique dans les deux langages.

## Commandes

- Tous les tests : `make test`
- C++ : `make test-cpp` (CMake + Ninja, GoogleTest)
- C++ avec sanitizers : `cmake -S cpp -B cpp/build-asan -DTOURNOI_SANITIZERS=ON`
- PHP : `make test-php` (PHPUnit 11)

## Conventions

- Identifiants et commentaires en français (`genererRoundRobin`, `Journee`, `exempte`).
- C++20, namespace `tournoi`, en-têtes dans `cpp/include/tournoi/`.
- PHP 8.3 avec `declare(strict_types=1)`, namespace `Tournoi`, classes `final`.
- Toute modification de comportement s'accompagne d'un test, dans les deux langages.
- Lancer `make test` avant de proposer un commit.
