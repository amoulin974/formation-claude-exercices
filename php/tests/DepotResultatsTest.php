<?php

declare(strict_types=1);

namespace Tournoi\Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use Tournoi\DepotResultats;
use Tournoi\Resultat;

final class DepotResultatsTest extends TestCase
{
    private DepotResultats $depot;

    protected function setUp(): void
    {
        $this->depot = new DepotResultats(new PDO('sqlite::memory:'));
        $this->depot->ajouter(new Resultat('Anglet', 'Bayonne', 0, 3));
        $this->depot->ajouter(new Resultat('Dax', 'Pau', 2, 0));
    }

    public function testRendTousLesResultatsDansLOrdre(): void
    {
        $tous = $this->depot->tous();
        self::assertCount(2, $tous);
        self::assertSame('Anglet', $tous[0]->domicile);
        self::assertSame(3, $tous[0]->butsExterieur);
    }

    public function testFiltreParEquipe(): void
    {
        $resultats = $this->depot->deLEquipe('Pau');
        self::assertCount(1, $resultats);
        self::assertSame('Dax', $resultats[0]->domicile);
    }
}
