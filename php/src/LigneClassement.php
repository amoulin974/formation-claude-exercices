<?php

declare(strict_types=1);

namespace Tournoi;

final class LigneClassement
{
    public int $joues = 0;
    public int $gagnes = 0;
    public int $nuls = 0;
    public int $perdus = 0;
    public int $butsPour = 0;
    public int $butsContre = 0;
    public int $points = 0;

    public function __construct(public readonly string $equipe)
    {
    }

    public function difference(): int
    {
        return $this->butsPour - $this->butsContre;
    }
}
