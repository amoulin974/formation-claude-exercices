<?php

declare(strict_types=1);

namespace Tournoi;

use InvalidArgumentException;
use RuntimeException;

final class Classement
{
    /** Barème : 3 points pour une victoire, 1 pour un nul, 0 pour une défaite. */
    public const POINTS_VICTOIRE = 3;
    public const POINTS_NUL = 1;

    /**
     * Calcule le classement à partir des résultats.
     * Ordre : points, puis différence de buts, puis buts marqués, puis nom d'équipe.
     *
     * @param list<Resultat> $resultats
     * @return list<LigneClassement>
     */
    public static function calculer(array $resultats): array
    {
        /** @var array<string, LigneClassement> $lignes */
        $lignes = [];

        foreach ($resultats as $r) {
            $dom = $lignes[$r->domicile] ??= new LigneClassement($r->domicile);
            $dom->butsPour += $r->butsDomicile;
            $dom->butsContre += $r->butsExterieur;
            self::enregistrerScore($dom, $r->butsDomicile, $r->butsExterieur);

            $ext = $lignes[$r->exterieur] ??= new LigneClassement($r->exterieur);
            $ext->butsPour += $r->butsExterieur;
            $ext->butsContre += $r->butsExterieur;
            self::enregistrerScore($ext, $r->butsExterieur, $r->butsDomicile);
        }

        $classement = array_values($lignes);
        usort($classement, fn (LigneClassement $a, LigneClassement $b) =>
            [$b->points, $b->difference(), $b->butsPour, $a->equipe]
            <=> [$a->points, $a->difference(), $a->butsPour, $b->equipe]);

        return $classement;
    }

    /**
     * Lit des résultats au format CSV : domicile,exterieur,buts_domicile,buts_exterieur
     * La première ligne (en-tête) est ignorée.
     *
     * @return list<Resultat>
     */
    public static function lireCsv(string $chemin): array
    {
        $fichier = @fopen($chemin, 'r');
        if ($fichier === false) {
            throw new RuntimeException("Impossible d'ouvrir $chemin");
        }

        $resultats = [];
        $numero = 0;
        try {
            while (($champs = fgetcsv($fichier, escape: '')) !== false) {
                $numero++;
                if ($numero === 1 || $champs === [null]) {
                    continue;
                }
                if (count($champs) !== 4) {
                    throw new InvalidArgumentException("Ligne $numero : 4 champs attendus");
                }
                [$domicile, $exterieur, $butsDomicile, $butsExterieur] = array_map('trim', $champs);
                if (!ctype_digit($butsDomicile) || !ctype_digit($butsExterieur)) {
                    throw new InvalidArgumentException("Ligne $numero : score invalide");
                }
                $resultats[] = new Resultat($domicile, $exterieur, (int) $butsDomicile, (int) $butsExterieur);
            }
        } finally {
            fclose($fichier);
        }

        return $resultats;
    }

    private static function enregistrerScore(LigneClassement $ligne, int $marques, int $encaisses): void
    {
        $ligne->joues++;
        if ($marques > $encaisses) {
            $ligne->gagnes++;
            $ligne->points += self::POINTS_VICTOIRE;
        } elseif ($marques === $encaisses) {
            $ligne->nuls++;
            $ligne->points += self::POINTS_NUL;
        } else {
            $ligne->perdus++;
        }
    }
}
