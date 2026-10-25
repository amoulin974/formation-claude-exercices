"""Agent minimal : un modèle, une boucle, trois outils.

Usage :
    export ANTHROPIC_API_KEY=...
    python agent/mini_agent.py "Les tests passent-ils ? Résume l'organisation du dépôt."

Les outils sont volontairement limités : lecture seule, et une liste blanche
de commandes de test. Ajouter un outil d'écriture fait partie de l'exercice.
"""

import json
import os
import subprocess
import sys
from pathlib import Path

MODELE = os.environ.get("MODELE_CLAUDE", "claude-sonnet-5-5")
RACINE = Path(__file__).resolve().parent.parent
TAILLE_MAX = 20_000  # caractères renvoyés au modèle par résultat d'outil

COMMANDES_AUTORISEES = {
    "make test": ["make", "test"],
    "make test-cpp": ["make", "test-cpp"],
    "make test-php": ["make", "test-php"],
}

SYSTEME = (
    "Tu es un assistant de développement. Tu travailles sur le dépôt situé dans "
    f"{RACINE}. Utilise les outils pour lire le code et lancer les tests avant de "
    "répondre. Réponds en français, de façon concise."
)

OUTILS = [
    {
        "name": "lister_fichiers",
        "description": "Liste les fichiers suivis par git dans le dépôt.",
        "input_schema": {"type": "object", "properties": {}},
    },
    {
        "name": "lire_fichier",
        "description": "Lit un fichier texte du dépôt et renvoie son contenu.",
        "input_schema": {
            "type": "object",
            "properties": {
                "chemin": {
                    "type": "string",
                    "description": "Chemin relatif à la racine du dépôt, par exemple cpp/src/classement.cpp",
                }
            },
            "required": ["chemin"],
        },
    },
    {
        "name": "lancer_tests",
        "description": "Lance une commande de test et renvoie sa sortie et son code de retour.",
        "input_schema": {
            "type": "object",
            "properties": {"commande": {"type": "string", "enum": sorted(COMMANDES_AUTORISEES)}},
            "required": ["commande"],
        },
    },
]


def tronquer(texte: str) -> str:
    if len(texte) <= TAILLE_MAX:
        return texte
    return texte[:TAILLE_MAX] + f"\n[… {len(texte) - TAILLE_MAX} caractères tronqués]"


def executer_outil(nom: str, entree: dict) -> str:
    """Exécute un outil demandé par le modèle. Toute erreur est renvoyée au modèle sous forme de texte."""
    if nom == "lister_fichiers":
        sortie = subprocess.run(["git", "ls-files"], cwd=RACINE, capture_output=True, text=True)
        return tronquer(sortie.stdout)

    if nom == "lire_fichier":
        chemin = (RACINE / entree.get("chemin", "")).resolve()
        if not chemin.is_relative_to(RACINE):
            return "Refusé : le fichier doit se trouver dans le dépôt."
        if not chemin.is_file():
            return f"Fichier introuvable : {entree.get('chemin')}"
        return tronquer(chemin.read_text(encoding="utf-8", errors="replace"))

    if nom == "lancer_tests":
        commande = COMMANDES_AUTORISEES.get(entree.get("commande", ""))
        if commande is None:
            return f"Refusé : commandes autorisées : {', '.join(sorted(COMMANDES_AUTORISEES))}."
        try:
            sortie = subprocess.run(commande, cwd=RACINE, capture_output=True, text=True, timeout=600)
        except subprocess.TimeoutExpired:
            return "La commande a dépassé le délai de 10 minutes."
        return tronquer(f"Code de retour : {sortie.returncode}\n{sortie.stdout}\n{sortie.stderr}")

    return f"Outil inconnu : {nom}"


def boucle(client, demande: str, journal=print, max_tours: int = 20) -> str:
    """La boucle de l'agent : appeler le modèle, exécuter ses outils, lui renvoyer les résultats."""
    messages = [{"role": "user", "content": demande}]

    for _ in range(max_tours):
        reponse = client.messages.create(
            model=MODELE,
            max_tokens=4096,
            system=SYSTEME,
            tools=OUTILS,
            messages=messages,
        )
        messages.append({"role": "assistant", "content": reponse.content})

        if reponse.stop_reason != "tool_use":
            return "".join(bloc.text for bloc in reponse.content if bloc.type == "text")

        resultats = []
        for bloc in reponse.content:
            if bloc.type == "tool_use":
                journal(f"→ {bloc.name}({json.dumps(bloc.input, ensure_ascii=False)})")
                resultats.append(
                    {
                        "type": "tool_result",
                        "tool_use_id": bloc.id,
                        "content": executer_outil(bloc.name, bloc.input),
                    }
                )
        messages.append({"role": "user", "content": resultats})

    return "Arrêt : nombre maximal de tours atteint."


def main() -> None:
    import anthropic  # importé ici pour que les tests n'aient pas besoin du SDK

    demande = " ".join(sys.argv[1:]) or "Résume l'organisation de ce dépôt, puis vérifie que les tests passent."
    print(boucle(anthropic.Anthropic(), demande))


if __name__ == "__main__":
    main()
