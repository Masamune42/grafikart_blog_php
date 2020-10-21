# Projet Grafikart PHP

## Packages installés
- altorouter/altorouter : Permet de faire le routing vers les views
- symfony/var-dumper : Permet de faire de jolis var_dump
- filp/whoops : Permet d'afficher de belles pages d'erreurs et les erreurs sur les fichiers concernés (avec la ligne concernée)

## Tips
### Si on veut utiliser un fichier PHP avec des fonctions dedans pour les utiliser, on peut demander à l'autoloader de le faire :
1. On crée un fichier PHP dans src
2. On définie le chemin vers ce fichier dans "files" dans composer.json :
```json
"autoload": {
        "files": ["src/helpers.php"],
        "psr-4": {
            "App\\": "src/"
        }
    }
```
3. On utilise la commande : composer dump-autoload pour recréer l'autoloader

### Redirection mis en caches
Les redirections (301) sont mises en cache par le navigateur, on peut désactiver une redirection qui était présente, qui a été changée, mais toujours active :
- On ouvre le DevTools (F12)
- Dans Network -> Disable Cache