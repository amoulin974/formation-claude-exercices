#include <iostream>

#include "tournoi/calendrier.hpp"

int main(int argc, char* argv[]) {
    std::vector<std::string> equipes(argv + 1, argv + argc);
    if (equipes.empty()) {
        equipes = {"Anglet", "Bayonne", "Biarritz", "Dax", "Pau"};
    }

    try {
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
    } catch (const std::exception& e) {
        std::cerr << "Erreur : " << e.what() << '\n';
        return 1;
    }
    return 0;
}
