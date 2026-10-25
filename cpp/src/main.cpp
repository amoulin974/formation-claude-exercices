#include <fstream>
#include <iomanip>
#include <iostream>
#include <string>

#include "tournoi/calendrier.hpp"
#include "tournoi/classement.hpp"

namespace {

void usage() {
    std::cerr << "Usage :\n"
                 "  tournoi-cli calendrier EQUIPE EQUIPE [EQUIPE...]\n"
                 "  tournoi-cli classement FICHIER.csv\n";
}

int afficherCalendrier(const std::vector<std::string>& equipes) {
    const auto calendrier = tournoi::genererRoundRobin(equipes);
    for (std::size_t j = 0; j < calendrier.size(); ++j) {
        std::cout << "Journée " << j + 1 << '\n';
        for (const auto& m : calendrier[j].matchs) {
            std::cout << "  " << m.domicile << " - " << m.exterieur << '\n';
        }
        if (calendrier[j].exempte) {
            std::cout << "  Exempte : " << *calendrier[j].exempte << '\n';
        }
    }
    return 0;
}

int afficherClassement(const std::string& chemin) {
    std::ifstream fichier(chemin);
    if (!fichier) {
        std::cerr << "Erreur : impossible d'ouvrir " << chemin << '\n';
        return 1;
    }
    const auto classement = tournoi::calculerClassement(tournoi::lireResultatsCsv(fichier));

    std::cout << " #  Équipe        J   G   N   P   BP  BC  Diff  Pts\n";
    int rang = 0;
    for (const auto& l : classement) {
        std::cout << std::setw(2) << ++rang << "  " << std::left << std::setw(12) << l.equipe
                  << std::right << std::setw(3) << l.joues << std::setw(4) << l.gagnes
                  << std::setw(4) << l.nuls << std::setw(4) << l.perdus << std::setw(5)
                  << l.butsPour << std::setw(4) << l.butsContre << std::setw(6)
                  << std::showpos << l.difference() << std::noshowpos << std::setw(5)
                  << l.points << '\n';
    }
    return 0;
}

}  // namespace

int main(int argc, char* argv[]) {
    if (argc < 3) {
        usage();
        return 2;
    }
    const std::string commande = argv[1];

    try {
        if (commande == "calendrier") {
            return afficherCalendrier({argv + 2, argv + argc});
        }
        if (commande == "classement") {
            return afficherClassement(argv[2]);
        }
    } catch (const std::exception& e) {
        std::cerr << "Erreur : " << e.what() << '\n';
        return 1;
    }
    usage();
    return 2;
}
