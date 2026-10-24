<?php

declare(strict_types=1);

namespace Tournoi;

final readonly class Rencontre
{
    public function __construct(
        public string $domicile,
        public string $exterieur,
    ) {
    }
}
