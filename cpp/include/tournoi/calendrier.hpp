#pragma once

#include <optional>
#include <string>
#include <vector>

namespace tournoi {

struct Match {
    std::string domicile;
    std::string exterieur;
};

// Une journée regroupe les matchs joués en même temps.
// Avec un nombre impair d'équipes, une équipe est exemptée à chaque journée.
struct Journee {
    std::vector<Match> matchs;
    std::optional<std::string> exempte;
};

// Génère un calendrier où chaque équipe rencontre chaque autre une seule fois.
// Lève std::invalid_argument si moins de deux équipes ou en cas de doublon.
std::vector<Journee> genererRoundRobin(const std::vector<std::string>& equipes);

}  // namespace tournoi
