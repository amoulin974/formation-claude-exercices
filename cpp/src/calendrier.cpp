#include "tournoi/calendrier.hpp"

#include <set>
#include <stdexcept>

namespace tournoi {

std::vector<Journee> genererRoundRobin(const std::vector<std::string>& equipes) {
    if (equipes.size() < 2) {
        throw std::invalid_argument("Il faut au moins deux équipes");
    }
    if (std::set<std::string>(equipes.begin(), equipes.end()).size() != equipes.size()) {
        throw std::invalid_argument("Les noms d'équipes doivent être uniques");
    }

    // Méthode du cercle : une case vide sert d'exemption si le nombre est impair.
    std::vector<std::optional<std::string>> cercle(equipes.begin(), equipes.end());
    if (cercle.size() % 2 == 1) {
        cercle.push_back(std::nullopt);
    }

    const std::size_t n = cercle.size();
    std::vector<Journee> calendrier;

    for (std::size_t tour = 0; tour < n - 1; ++tour) {
        Journee journee;
        for (std::size_t i = 0; i < n / 2; ++i) {
            const auto& a = cercle[i];
            const auto& b = cercle[n - 1 - i];
            if (!a || !b) {
                journee.exempte = a ? a : b;
            } else if (tour % 2 == 0) {
                journee.matchs.push_back({*a, *b});
            } else {
                journee.matchs.push_back({*b, *a});
            }
        }
        calendrier.push_back(journee);

        // Le premier élément reste fixe, les autres tournent d'un cran.
        auto dernier = cercle.back();
        cercle.pop_back();
        cercle.insert(cercle.begin() + 1, dernier);
    }
    return calendrier;
}

}  // namespace tournoi
