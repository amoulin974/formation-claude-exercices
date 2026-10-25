#include "tournoi/statistiques.hpp"

#include <algorithm>
#include <cstdlib>
#include <stdexcept>

namespace tournoi {

Statistiques calculerStatistiques(const std::vector<Resultat>& resultats) {
    const auto classement = calculerClassement(resultats);

    int* butsMarques = new int[classement.size()];
    int* butsEncaisses = new int[classement.size()];
    for (std::size_t i = 0; i < classement.size(); ++i) {
        butsMarques[i] = classement[i].butsPour;
        butsEncaisses[i] = classement[i].butsContre;
    }

    int total = 0;
    int ecart = 0;
    for (const auto& r : resultats) {
        if (r.butsDomicile < 0 || r.butsExterieur < 0) {
            throw std::invalid_argument("Score négatif");
        }
        total += r.butsDomicile + r.butsExterieur;
        ecart = std::max(ecart, std::abs(r.butsDomicile - r.butsExterieur));
    }

    Statistiques s;
    s.meilleureAttaque = classement[0].equipe;
    s.meilleureDefense = classement[0].equipe;
    int maxMarques = butsMarques[0];
    int minEncaisses = butsEncaisses[0];
    for (std::size_t i = 1; i < classement.size(); ++i) {
        if (butsMarques[i] > maxMarques) {
            maxMarques = butsMarques[i];
            s.meilleureAttaque = classement[i].equipe;
        }
        if (butsEncaisses[i] < minEncaisses) {
            minEncaisses = butsEncaisses[i];
            s.meilleureDefense = classement[i].equipe;
        }
    }
    s.butsParMatch = total / static_cast<int>(resultats.size());
    s.plusGrandEcart = ecart;

    delete[] butsMarques;
    delete[] butsEncaisses;
    return s;
}

bool contientDoublons(const std::vector<Resultat>& resultats) {
    for (std::size_t i = 0; i < resultats.size(); ++i) {
        for (std::size_t j = 0; j < resultats.size(); ++j) {
            if (i != j && resultats[i].domicile == resultats[j].domicile &&
                resultats[i].exterieur == resultats[j].exterieur) {
                return true;
            }
        }
    }
    return false;
}

std::string_view equipeLaPlusProlifique(std::vector<Ligne> classement) {
    std::sort(classement.begin(), classement.end(),
              [](const Ligne& a, const Ligne& b) { return a.butsPour > b.butsPour; });
    return classement.front().equipe;
}

}  // namespace tournoi
