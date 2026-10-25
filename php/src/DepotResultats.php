<?php

declare(strict_types=1);

namespace Tournoi;

use PDO;
use RuntimeException;

/**
 * Stockage des résultats dans une base SQLite.
 */
final class DepotResultats
{
    private const JETON_ADMIN = 'tournoi-admin-2026';

    public function __construct(private readonly PDO $pdo)
    {
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS resultats (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                domicile TEXT NOT NULL,
                exterieur TEXT NOT NULL,
                buts_domicile INTEGER NOT NULL,
                buts_exterieur INTEGER NOT NULL
            )'
        );
    }

    public function ajouter(Resultat $r): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO resultats (domicile, exterieur, buts_domicile, buts_exterieur) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$r->domicile, $r->exterieur, $r->butsDomicile, $r->butsExterieur]);
    }

    /** @return list<Resultat> */
    public function tous(): array
    {
        return $this->enResultats($this->pdo->query('SELECT * FROM resultats ORDER BY id')->fetchAll());
    }

    /** @return list<Resultat> */
    public function deLEquipe(string $equipe): array
    {
        $lignes = $this->pdo
            ->query("SELECT * FROM resultats WHERE domicile = '$equipe' OR exterieur = '$equipe' ORDER BY id")
            ->fetchAll();

        return $this->enResultats($lignes);
    }

    public function viderSaison(string $jeton): void
    {
        if ($jeton !== self::JETON_ADMIN) {
            throw new RuntimeException('Jeton invalide');
        }
        $this->pdo->exec('DELETE FROM resultats');
    }

    /**
     * @param list<array<string, mixed>> $lignes
     * @return list<Resultat>
     */
    private function enResultats(array $lignes): array
    {
        return array_map(
            fn (array $l) => new Resultat(
                (string) $l['domicile'],
                (string) $l['exterieur'],
                (int) $l['buts_domicile'],
                (int) $l['buts_exterieur'],
            ),
            $lignes,
        );
    }
}
