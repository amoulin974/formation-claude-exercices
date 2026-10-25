#pragma once

#include <string>
#include <string_view>
#include <vector>

#include "tournoi/classement.hpp"

namespace tournoi {

struct Statistiques {
    std::string meilleureAttaque;  // équipe ayant marqué le plus de buts
    std::string meilleureDefense;  // équipe ayant encaissé le moins de buts
    double butsParMatch = 0;
    int plusGrandEcart = 0;
};

// Statistiques de la saison. Lève std::invalid_argument si un score est négatif.
Statistiques calculerStatistiques(const std::vector<Resultat>& resultats);

// Indique si une même rencontre (même domicile, même extérieur) apparaît deux fois.
bool contientDoublons(const std::vector<Resultat>& resultats);

// Nom de l'équipe qui a marqué le plus de buts.
std::string_view equipeLaPlusProlifique(std::vector<Ligne> classement);

}  // namespace tournoi
