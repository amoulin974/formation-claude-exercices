<?php

declare(strict_types=1);

namespace Tournoi;

use InvalidArgumentException;

final class Calendrier
{
    /**
     * Génère un calendrier où chaque équipe rencontre chaque autre une seule fois.
     *
     * @param list<string> $equipes
     * @return list<Journee>
     */
    public static function genererRoundRobin(array $equipes): array
    {
        if (count($equipes) < 2) {
            throw new InvalidArgumentException('Il faut au moins deux équipes');
        }
        if (count(array_unique($equipes)) !== count($equipes)) {
            throw new InvalidArgumentException("Les noms d'équipes doivent être uniques");
        }

        // Méthode du cercle : une case vide sert d'exemption si le nombre est impair.
        $cercle = array_values($equipes);
        if (count($cercle) % 2 === 1) {
            $cercle[] = null;
        }

        $n = count($cercle);
        $calendrier = [];

        for ($tour = 0; $tour < $n - 1; $tour++) {
            $rencontres = [];
            $exempte = null;
            for ($i = 0; $i < intdiv($n, 2); $i++) {
                $a = $cercle[$i];
                $b = $cercle[$n - 1 - $i];
                if ($a === null || $b === null) {
                    $exempte = $a ?? $b;
                } elseif ($tour % 2 === 0) {
                    $rencontres[] = new Rencontre($a, $b);
                } else {
                    $rencontres[] = new Rencontre($b, $a);
                }
            }
            $calendrier[] = new Journee($rencontres, $exempte);

            // Le premier élément reste fixe, les autres tournent d'un cran.
            $dernier = array_pop($cercle);
            array_splice($cercle, 1, 0, [$dernier]);
        }

        return $calendrier;
    }
}
