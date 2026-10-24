#include <gtest/gtest.h>

#include <map>
#include <set>
#include <stdexcept>

#include "tournoi/calendrier.hpp"

using tournoi::genererRoundRobin;

namespace {
std::vector<std::string> equipes(int n) {
    std::vector<std::string> v;
    for (int i = 0; i < n; ++i) v.push_back("E" + std::to_string(i));
    return v;
}
}  // namespace

TEST(RoundRobin, NombreDeJourneesPair) {
    EXPECT_EQ(genererRoundRobin(equipes(6)).size(), 5u);
}

TEST(RoundRobin, NombreDeJourneesImpair) {
    EXPECT_EQ(genererRoundRobin(equipes(5)).size(), 5u);
}

TEST(RoundRobin, ChaquePaireSeRencontreUneFois) {
    for (int n : {2, 3, 4, 7, 10}) {
        std::map<std::pair<std::string, std::string>, int> rencontres;
        for (const auto& j : genererRoundRobin(equipes(n))) {
            for (const auto& m : j.matchs) {
                auto paire = std::minmax(m.domicile, m.exterieur);
                ++rencontres[{paire.first, paire.second}];
            }
        }
        EXPECT_EQ(rencontres.size(), static_cast<std::size_t>(n * (n - 1) / 2)) << "n=" << n;
        for (const auto& [paire, nb] : rencontres) EXPECT_EQ(nb, 1);
    }
}

TEST(RoundRobin, UneEquipeJoueAuPlusUneFoisParJournee) {
    for (const auto& j : genererRoundRobin(equipes(8))) {
        std::set<std::string> vues;
        for (const auto& m : j.matchs) {
            EXPECT_TRUE(vues.insert(m.domicile).second);
            EXPECT_TRUE(vues.insert(m.exterieur).second);
        }
    }
}

TEST(RoundRobin, ExemptionAvecNombreImpair) {
    std::set<std::string> exemptes;
    for (const auto& j : genererRoundRobin(equipes(5))) {
        ASSERT_TRUE(j.exempte.has_value());
        exemptes.insert(*j.exempte);
    }
    EXPECT_EQ(exemptes.size(), 5u);
}

TEST(RoundRobin, RefuseMoinsDeDeuxEquipes) {
    EXPECT_THROW(genererRoundRobin({"Seule"}), std::invalid_argument);
}

TEST(RoundRobin, RefuseLesDoublons) {
    EXPECT_THROW(genererRoundRobin({"A", "B", "A"}), std::invalid_argument);
}
