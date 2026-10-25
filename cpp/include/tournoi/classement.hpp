#pragma once

#include <istream>
#include <string>
#include <vector>

namespace tournoi {

struct Resultat {
    std::string domicile;
    std::string exterieur;
    int butsDomicile = 0;
    int butsExterieur = 0;
};

struct Ligne {
    std::string equipe;
    int joues = 0;
    int gagnes = 0;
    int nuls = 0;
    int perdus = 0;
    int butsPour = 0;
    int butsContre = 0;
    int points = 0;

    int difference() const { return butsPour - butsContre; }
};

// Barème : 3 points pour une victoire, 1 pour un nul, 0 pour une défaite.
inline constexpr int kPointsVictoire = 3;
inline constexpr int kPointsNul = 1;

// Calcule le classement à partir des résultats.
// Ordre : points, puis différence de buts, puis buts marqués, puis nom d'équipe.
std::vector<Ligne> calculerClassement(const std::vector<Resultat>& resultats);

// Lit des résultats au format CSV : domicile,exterieur,buts_domicile,buts_exterieur
// La première ligne (en-tête) est ignorée.
// Lève std::invalid_argument si une ligne est mal formée.
std::vector<Resultat> lireResultatsCsv(std::istream& entree);

}  // namespace tournoi
