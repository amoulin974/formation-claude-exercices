<?php

declare(strict_types=1);

namespace Tournoi\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tournoi\Calendrier;

final class CalendrierTest extends TestCase
{
    /** @return list<string> */
    private static function equipes(int $n): array
    {
        return array_map(fn (int $i) => "E$i", range(0, $n - 1));
    }

    public function testNombreDeJourneesPair(): void
    {
        self::assertCount(5, Calendrier::genererRoundRobin(self::equipes(6)));
    }

    public function testNombreDeJourneesImpair(): void
    {
        self::assertCount(5, Calendrier::genererRoundRobin(self::equipes(5)));
    }

    /** @return iterable<array{int}> */
    public static function tailles(): iterable
    {
        foreach ([2, 3, 4, 7, 10] as $n) {
            yield "$n équipes" => [$n];
        }
    }

    #[DataProvider('tailles')]
    public function testChaquePaireSeRencontreUneFois(int $n): void
    {
        $rencontres = [];
        foreach (Calendrier::genererRoundRobin(self::equipes($n)) as $journee) {
            foreach ($journee->rencontres as $r) {
                $paire = [$r->domicile, $r->exterieur];
                sort($paire);
                $cle = implode('|', $paire);
                $rencontres[$cle] = ($rencontres[$cle] ?? 0) + 1;
            }
        }
        self::assertCount(intdiv($n * ($n - 1), 2), $rencontres);
        foreach ($rencontres as $nb) {
            self::assertSame(1, $nb);
        }
    }

    public function testUneEquipeJoueAuPlusUneFoisParJournee(): void
    {
        foreach (Calendrier::genererRoundRobin(self::equipes(8)) as $journee) {
            $vues = [];
            foreach ($journee->rencontres as $r) {
                $vues[] = $r->domicile;
                $vues[] = $r->exterieur;
            }
            self::assertSame(count($vues), count(array_unique($vues)));
        }
    }

    public function testExemptionAvecNombreImpair(): void
    {
        $exemptes = [];
        foreach (Calendrier::genererRoundRobin(self::equipes(5)) as $journee) {
            self::assertNotNull($journee->exempte);
            $exemptes[] = $journee->exempte;
        }
        self::assertCount(5, array_unique($exemptes));
    }

    public function testRefuseMoinsDeDeuxEquipes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Calendrier::genererRoundRobin(['Seule']);
    }

    public function testRefuseLesDoublons(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Calendrier::genererRoundRobin(['A', 'B', 'A']);
    }
}
