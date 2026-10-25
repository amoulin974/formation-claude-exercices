#include <gtest/gtest.h>

#include <sstream>
#include <stdexcept>

#include "tournoi/classement.hpp"

using tournoi::calculerClassement;
using tournoi::lireResultatsCsv;

TEST(Classement, VictoireRapporteTroisPoints) {
    const auto c = calculerClassement({{"A", "B", 2, 0}});
    ASSERT_EQ(c.size(), 2u);
    EXPECT_EQ(c[0].equipe, "A");
    EXPECT_EQ(c[0].points, 3);
    EXPECT_EQ(c[0].gagnes, 1);
    EXPECT_EQ(c[1].equipe, "B");
    EXPECT_EQ(c[1].points, 0);
    EXPECT_EQ(c[1].perdus, 1);
}

TEST(Classement, NulRapporteUnPointAChacun) {
    const auto c = calculerClassement({{"A", "B", 1, 1}});
    for (const auto& l : c) {
        EXPECT_EQ(l.points, 1);
        EXPECT_EQ(l.nuls, 1);
    }
}

TEST(Classement, CompteLesMatchsJoues) {
    const auto c = calculerClassement({{"A", "B", 1, 0}, {"B", "C", 2, 2}, {"C", "A", 0, 3}});
    for (const auto& l : c) EXPECT_EQ(l.joues, 2);
}

TEST(Classement, TrieParPoints) {
    const auto c = calculerClassement({{"A", "B", 0, 1}, {"C", "A", 2, 0}, {"C", "B", 1, 0}});
    ASSERT_EQ(c.size(), 3u);
    EXPECT_EQ(c[0].equipe, "C");
    EXPECT_EQ(c[1].equipe, "B");
    EXPECT_EQ(c[2].equipe, "A");
}

TEST(Classement, DepartageParLeNomEnDernierRecours) {
    const auto c = calculerClassement({{"Zarautz", "Anglet", 0, 0}});
    EXPECT_EQ(c[0].equipe, "Anglet");
    EXPECT_EQ(c[1].equipe, "Zarautz");
}

TEST(Csv, LitLesResultats) {
    std::istringstream csv("domicile,exterieur,buts_domicile,buts_exterieur\nA,B,2,1\r\nC,D,0,0\n");
    const auto r = lireResultatsCsv(csv);
    ASSERT_EQ(r.size(), 2u);
    EXPECT_EQ(r[0].domicile, "A");
    EXPECT_EQ(r[0].butsDomicile, 2);
    EXPECT_EQ(r[1].exterieur, "D");
}

TEST(Csv, RefuseUneLigneIncomplete) {
    std::istringstream csv("domicile,exterieur,buts_domicile,buts_exterieur\nA,B,2\n");
    EXPECT_THROW(lireResultatsCsv(csv), std::invalid_argument);
}

TEST(Csv, RefuseUnScoreNonNumerique) {
    std::istringstream csv("domicile,exterieur,buts_domicile,buts_exterieur\nA,B,deux,1\n");
    EXPECT_THROW(lireResultatsCsv(csv), std::invalid_argument);
}
