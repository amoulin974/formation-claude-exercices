<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Tournoi\Calendrier;
use Tournoi\Classement;

$usage = <<<TXT
    Usage :
      php bin/tournoi.php calendrier EQUIPE EQUIPE [EQUIPE...]
      php bin/tournoi.php classement FICHIER.csv

    TXT;

if ($argc < 3) {
    fwrite(STDERR, $usage);
    exit(2);
}

try {
    switch ($argv[1]) {
        case 'calendrier':
            foreach (Calendrier::genererRoundRobin(array_slice($argv, 2)) as $i => $journee) {
                echo 'Journée ', $i + 1, "\n";
                foreach ($journee->rencontres as $r) {
                    echo "  {$r->domicile} - {$r->exterieur}\n";
                }
                if ($journee->exempte !== null) {
                    echo "  Exempte : {$journee->exempte}\n";
                }
            }
            break;

        case 'classement':
            $classement = Classement::calculer(Classement::lireCsv($argv[2]));
            echo " #  Équipe        J   G   N   P   BP  BC  Diff  Pts\n";
            foreach ($classement as $i => $l) {
                printf(
                    "%2d  %-12s%3d%4d%4d%4d%5d%4d%6s%5d\n",
                    $i + 1, $l->equipe, $l->joues, $l->gagnes, $l->nuls, $l->perdus,
                    $l->butsPour, $l->butsContre, sprintf('%+d', $l->difference()), $l->points,
                );
            }
            break;

        default:
            fwrite(STDERR, $usage);
            exit(2);
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Erreur : ' . $e->getMessage() . "\n");
    exit(1);
}
