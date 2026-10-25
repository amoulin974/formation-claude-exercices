<?php

declare(strict_types=1);

namespace Tournoi;

final readonly class Resultat
{
    public function __construct(
        public string $domicile,
        public string $exterieur,
        public int $butsDomicile,
        public int $butsExterieur,
    ) {
    }
}
