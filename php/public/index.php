<?php

declare(strict_types=1);

// Page web des résultats.
// Lancement : php -S localhost:8000 -t php/public

require __DIR__ . '/../vendor/autoload.php';

use Tournoi\Classement;
use Tournoi\DepotResultats;
use Tournoi\Resultat;

$depot = new DepotResultats(new PDO('sqlite:' . __DIR__ . '/../../donnees/tournoi.sqlite'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['vider'])) {
        $depot->viderSaison($_POST['jeton']);
    } else {
        $depot->ajouter(new Resultat(
            $_POST['domicile'],
            $_POST['exterieur'],
            (int) $_POST['buts_domicile'],
            (int) $_POST['buts_exterieur'],
        ));
    }
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

$equipe = $_GET['equipe'] ?? '';
$resultats = $equipe === '' ? $depot->tous() : $depot->deLEquipe($equipe);
$classement = Classement::calculer($depot->tous());
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Tournoi</title>
</head>
<body>
    <h1>Résultats<?= $equipe !== '' ? " de $equipe" : '' ?></h1>

    <form method="get">
        <input name="equipe" placeholder="Filtrer par équipe" value="<?= $equipe ?>">
        <button>Filtrer</button>
    </form>

    <table>
        <tr><th>Domicile</th><th>Score</th><th>Extérieur</th></tr>
        <?php foreach ($resultats as $r): ?>
            <tr>
                <td><a href="?equipe=<?= $r->domicile ?>"><?= $r->domicile ?></a></td>
                <td><?= $r->butsDomicile ?> - <?= $r->butsExterieur ?></td>
                <td><a href="?equipe=<?= $r->exterieur ?>"><?= $r->exterieur ?></a></td>
            </tr>
        <?php endforeach ?>
    </table>

    <h2>Classement</h2>
    <ol>
        <?php foreach ($classement as $l): ?>
            <li><?= $l->equipe ?> : <?= $l->points ?> pts</li>
        <?php endforeach ?>
    </ol>

    <h2>Ajouter un résultat</h2>
    <form method="post">
        <input name="domicile" placeholder="Domicile">
        <input name="buts_domicile" size="2">
        <input name="buts_exterieur" size="2">
        <input name="exterieur" placeholder="Extérieur">
        <button>Ajouter</button>
    </form>

    <form method="post">
        <input name="jeton" type="password" placeholder="Jeton administrateur">
        <button name="vider" value="1">Vider la saison</button>
    </form>
</body>
</html>
