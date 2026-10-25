#include <gtest/gtest.h>

#include <stdexcept>

#include "tournoi/statistiques.hpp"

using tournoi::calculerStatistiques;
using tournoi::contientDoublons;

TEST(Statistiques, MeilleureAttaqueEtDefense) {
    const auto s = calculerStatistiques({{"A", "B", 2, 0}, {"B", "C", 3, 1}});
    EXPECT_EQ(s.meilleureAttaque, "B");
    EXPECT_EQ(s.meilleureDefense, "A");
}

TEST(Statistiques, ButsParMatch) {
    const auto s = calculerStatistiques({{"A", "B", 2, 0}, {"B", "C", 3, 1}});
    EXPECT_DOUBLE_EQ(s.butsParMatch, 3.0);
}

TEST(Statistiques, PlusGrandEcart) {
    const auto s = calculerStatistiques({{"A", "B", 2, 0}, {"B", "C", 5, 1}});
    EXPECT_EQ(s.plusGrandEcart, 4);
}

TEST(Statistiques, DetecteLesDoublons) {
    EXPECT_TRUE(contientDoublons({{"A", "B", 1, 0}, {"C", "D", 0, 0}, {"A", "B", 2, 2}}));
    EXPECT_FALSE(contientDoublons({{"A", "B", 1, 0}, {"B", "A", 0, 0}}));
}
