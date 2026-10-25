#include "tournoi/classement.hpp"

#include <algorithm>
#include <map>
#include <sstream>
#include <stdexcept>

namespace tournoi {

namespace {

void enregistrerScore(Ligne& ligne, int marques, int encaisses) {
    ++ligne.joues;
    if (marques > encaisses) {
        ++ligne.gagnes;
        ligne.points += kPointsVictoire;
    } else if (marques == encaisses) {
        ++ligne.nuls;
        ligne.points += kPointsNul;
    } else {
        ++ligne.perdus;
    }
}

}  // namespace

std::vector<Ligne> calculerClassement(const std::vector<Resultat>& resultats) {
    std::map<std::string, Ligne> lignes;

    for (const auto& r : resultats) {
        auto& dom = lignes[r.domicile];
        dom.equipe = r.domicile;
        dom.butsPour += r.butsDomicile;
        dom.butsContre += r.butsExterieur;
        enregistrerScore(dom, r.butsDomicile, r.butsExterieur);

        auto& ext = lignes[r.exterieur];
        ext.equipe = r.exterieur;
        ext.butsPour += r.butsExterieur;
        ext.butsContre += r.butsExterieur;
        enregistrerScore(ext, r.butsExterieur, r.butsDomicile);
    }

    std::vector<Ligne> classement;
    for (auto& [nom, ligne] : lignes) {
        classement.push_back(ligne);
    }

    std::sort(classement.begin(), classement.end(), [](const Ligne& a, const Ligne& b) {
        if (a.points != b.points) return a.points > b.points;
        if (a.difference() != b.difference()) return a.difference() > b.difference();
        if (a.butsPour != b.butsPour) return a.butsPour > b.butsPour;
        return a.equipe < b.equipe;
    });
    return classement;
}

std::vector<Resultat> lireResultatsCsv(std::istream& entree) {
    std::vector<Resultat> resultats;
    std::string ligne;
    std::getline(entree, ligne);  // en-tête

    int numero = 1;
    while (std::getline(entree, ligne)) {
        ++numero;
        if (!ligne.empty() && ligne.back() == '\r') ligne.pop_back();
        if (ligne.empty()) continue;

        std::vector<std::string> champs;
        std::stringstream flux(ligne);
        std::string champ;
        while (std::getline(flux, champ, ',')) champs.push_back(champ);

        if (champs.size() != 4) {
            throw std::invalid_argument("Ligne " + std::to_string(numero) + " : 4 champs attendus");
        }
        try {
            resultats.push_back({champs[0], champs[1], std::stoi(champs[2]), std::stoi(champs[3])});
        } catch (const std::logic_error&) {
            throw std::invalid_argument("Ligne " + std::to_string(numero) + " : score invalide");
        }
    }
    return resultats;
}

}  // namespace tournoi
