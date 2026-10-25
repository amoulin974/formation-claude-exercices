# Développer avec Claude : exercices

Code de départ des travaux pratiques de la formation « Développer avec Claude ».
Les énoncés sont sur le [site de la formation](https://amoulin974.github.io/formation-claude-site/).

Le même domaine, la gestion de tournoi, est implémenté dans deux langages :

| Dossier | Langage | Build et tests |
|---|---|---|
| `cpp/` | C++20 | CMake, GoogleTest |
| `php/` | PHP 8.3 | Composer, PHPUnit |

## Démarrer

1. Installez VS Code, l'extension **Dev Containers** et Docker.
2. Clonez ce dépôt, ouvrez-le dans VS Code, puis choisissez **Reopen in Container**.
3. Une fois le conteneur prêt, lancez les tests :

```bash
make test        # C++ et PHP
make test-cpp    # C++ seul
make test-php    # PHP seul
```

Sans Docker, ouvrez le dépôt dans **GitHub Codespaces** : la configuration est la même.

## Utiliser l'outil en ligne de commande

Les deux implémentations offrent les mêmes commandes.

```bash
# C++ (après make test-cpp)
cpp/build/tournoi-cli calendrier Anglet Bayonne Biarritz Dax
cpp/build/tournoi-cli classement donnees/resultats-2026.csv

# PHP
php php/bin/tournoi.php calendrier Anglet Bayonne Biarritz Dax
php php/bin/tournoi.php classement donnees/resultats-2026.csv
```

Le dossier `donnees/` contient les résultats d'une saison au format CSV
(`domicile,exterieur,buts_domicile,buts_exterieur`).

## Outils inclus dans le conteneur

g++, CMake, Ninja, GoogleTest, gdb, Valgrind, PHP 8.3, Composer, Xdebug, Node.js,
la CLI Claude Code et l'extension Claude Code pour VS Code.

La configuration Claude (`~/.claude`) est conservée dans un volume Docker :
vous restez connecté même après une reconstruction du conteneur.

## Sanitizers (C++)

```bash
cmake -S cpp -B cpp/build-asan -DTOURNOI_SANITIZERS=ON
cmake --build cpp/build-asan && ctest --test-dir cpp/build-asan
```
