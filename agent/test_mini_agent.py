"""Tests de l'agent minimal, avec un faux client : aucune clé d'API n'est nécessaire.

Lancer : python3 -m unittest discover -s agent
"""

import unittest
from types import SimpleNamespace

import mini_agent


def texte(contenu):
    return SimpleNamespace(type="text", text=contenu)


def appel_outil(identifiant, nom, entree):
    return SimpleNamespace(type="tool_use", id=identifiant, name=nom, input=entree)


class FauxClient:
    """Rejoue une suite de réponses prédéfinies et mémorise les requêtes reçues."""

    def __init__(self, reponses):
        self.reponses = list(reponses)
        self.requetes = []
        self.messages = self

    def create(self, **requete):
        self.requetes.append({**requete, "messages": list(requete["messages"])})
        return self.reponses.pop(0)


class TestOutils(unittest.TestCase):
    def test_lit_un_fichier_du_depot(self):
        self.assertIn("make test", mini_agent.executer_outil("lire_fichier", {"chemin": "CLAUDE.md"}))

    def test_refuse_un_fichier_hors_du_depot(self):
        resultat = mini_agent.executer_outil("lire_fichier", {"chemin": "../../etc/passwd"})
        self.assertTrue(resultat.startswith("Refusé"))

    def test_refuse_une_commande_hors_liste(self):
        resultat = mini_agent.executer_outil("lancer_tests", {"commande": "rm -rf /"})
        self.assertTrue(resultat.startswith("Refusé"))

    def test_liste_les_fichiers(self):
        self.assertIn("Makefile", mini_agent.executer_outil("lister_fichiers", {}))


class TestBoucle(unittest.TestCase):
    def test_execute_les_outils_puis_rend_la_reponse_finale(self):
        client = FauxClient(
            [
                SimpleNamespace(
                    stop_reason="tool_use",
                    content=[texte("Je lis le fichier."), appel_outil("t1", "lire_fichier", {"chemin": "Makefile"})],
                ),
                SimpleNamespace(stop_reason="end_turn", content=[texte("Le Makefile lance les tests.")]),
            ]
        )
        journal = []

        reponse = mini_agent.boucle(client, "Que fait le Makefile ?", journal=journal.append)

        self.assertEqual(reponse, "Le Makefile lance les tests.")
        self.assertEqual(len(client.requetes), 2)
        self.assertEqual(journal, ['→ lire_fichier({"chemin": "Makefile"})'])
        retour_outil = client.requetes[1]["messages"][-1]["content"][0]
        self.assertEqual(retour_outil["type"], "tool_result")
        self.assertEqual(retour_outil["tool_use_id"], "t1")
        self.assertIn("test-cpp", retour_outil["content"])

    def test_s_arrete_apres_le_nombre_maximal_de_tours(self):
        en_boucle = SimpleNamespace(stop_reason="tool_use", content=[appel_outil("t", "lister_fichiers", {})])
        client = FauxClient([en_boucle] * 3)

        reponse = mini_agent.boucle(client, "?", journal=lambda _: None, max_tours=3)

        self.assertIn("nombre maximal", reponse)


if __name__ == "__main__":
    unittest.main()
