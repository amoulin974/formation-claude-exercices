<?php

declare(strict_types=1);

namespace Tournoi;

/**
 * Une journée regroupe les rencontres jouées en même temps.
 * Avec un nombre impair d'équipes, une équipe est exemptée à chaque journée.
 */
final readonly class Journee
{
    /** @param list<Rencontre> $rencontres */
    public function __construct(
        public array $rencontres,
        public ?string $exempte = null,
    ) {
    }
}
