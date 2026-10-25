<?php

declare(strict_types=1);

namespace Tournoi\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Tournoi\Classement;
use Tournoi\Resultat;

final class ClassementTest extends TestCase
{
    public function testVictoireRapporteTroisPoints(): void
    {
        $c = Classement::calculer([new Resultat('A', 'B', 2, 0)]);
        self::assertCount(2, $c);
        self::assertSame('A', $c[0]->equipe);
        self::assertSame(3, $c[0]->points);
        self::assertSame(1, $c[0]->gagnes);
        self::assertSame('B', $c[1]->equipe);
        self::assertSame(0, $c[1]->points);
        self::assertSame(1, $c[1]->perdus);
    }

    public function testNulRapporteUnPointAChacun(): void
    {
        foreach (Classement::calculer([new Resultat('A', 'B', 1, 1)]) as $l) {
            self::assertSame(1, $l->points);
            self::assertSame(1, $l->nuls);
        }
    }

    public function testCompteLesMatchsJoues(): void
    {
        $c = Classement::calculer([
            new Resultat('A', 'B', 1, 0),
            new Resultat('B', 'C', 2, 2),
            new Resultat('C', 'A', 0, 3),
        ]);
        foreach ($c as $l) {
            self::assertSame(2, $l->joues);
        }
    }

    public function testTrieParPoints(): void
    {
        $c = Classement::calculer([
            new Resultat('A', 'B', 0, 1),
            new Resultat('C', 'A', 2, 0),
            new Resultat('C', 'B', 1, 0),
        ]);
        self::assertSame(['C', 'B', 'A'], array_map(fn ($l) => $l->equipe, $c));
    }

    public function testDepartageParLeNomEnDernierRecours(): void
    {
        $c = Classement::calculer([new Resultat('Zarautz', 'Anglet', 0, 0)]);
        self::assertSame(['Anglet', 'Zarautz'], array_map(fn ($l) => $l->equipe, $c));
    }

    public function testLitLesResultats(): void
    {
        $r = Classement::lireCsv(self::csv("domicile,exterieur,buts_domicile,buts_exterieur\nA,B,2,1\r\nC,D,0,0\n"));
        self::assertCount(2, $r);
        self::assertSame('A', $r[0]->domicile);
        self::assertSame(2, $r[0]->butsDomicile);
        self::assertSame('D', $r[1]->exterieur);
    }

    public function testRefuseUneLigneIncomplete(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Classement::lireCsv(self::csv("domicile,exterieur,buts_domicile,buts_exterieur\nA,B,2\n"));
    }

    public function testRefuseUnScoreNonNumerique(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Classement::lireCsv(self::csv("domicile,exterieur,buts_domicile,buts_exterieur\nA,B,deux,1\n"));
    }

    private static function csv(string $contenu): string
    {
        $chemin = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($chemin, $contenu);

        return $chemin;
    }
}
