# Projet Grafikart PHP

## Packages installés
- altorouter/altorouter : Permet de faire le routing vers les views
- symfony/var-dumper : Permet de faire de jolis var_dump
- filp/whoops : Permet d'afficher de belles pages d'erreurs et les erreurs sur les fichiers concernés (avec la ligne concernée)
- fzaninotto/faker : Permet de générer du texte aléatoire suivant le besoin
- Valitron\Validator : Permet de valider les données envoyés dans un formulaire

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


### Refactoring
Méthode de refactoring de code utilisé pour la pagination entre les pages **category/show.php** et **post/index.php** (vidéo : Réorganisation de la pagination) :
- On prend 2 pages avec un code similaire
- On regarde pour les 2 pages les éléments / variables qui changent et on les place en commentaire dans un des 2 fichiers -> ex : requête SQL
- Tous les éléments qui changent sont traduits en paramètres => $sqlListing, $classMapping, $sqlCount, $perPage
- On repère les paramètres externes, ce dont la fonction à besoin pour fonctionner => $pdo, $currentPage
- Parmi les paramètres externes, déterminer ceux qui sont nécessaires ou pas
- On regarde ensuite de quelles fonctions on a besoin

### Appel d'une classe qu'on ne réutilise pas
On peut déclarer de cette façon une classe si on veut utiliser une méthode sur une class qu'on ne réutilisera pas
```PHP
$category = (new CategoryTable($pdo))->find($id);
```

### Récupération des éléments d'un tableau dans uen variable
Si on retourne dans une fonction un tableau avec des éléments définis :
```PHP
 return [$posts, $paginatedQuery];
```
On peut par la suite les récupérer en appelant cette fonction avec :
```PHP
// 1. La fonction list()
list($posts, $pagination) = $table->findPaginated();
// 2. Un tableau -> valable que dans les nouvelles versions de PHP
[$posts, $pagination] = $table->findPaginated();
```

### Types de classe
- abstract : Classe qui est prévue pour être héritée
- final : Classe qui ne sera pas héritée

### Réécriture de fonctions d'une librairie
On peut réécrire une fonction qui existe dans une librairie afin de modifier son comportement (seulement si la fonction n'est pas privée)
```PHP
// On défini le namespace
namespace App;
// On utilise la classe de la fonction que l'on veut modifier
use Valitron\Validator as ValitronValidator;
// On étend de la classe de base
class Validator extends ValitronValidator
{
    // On redéclare la fonction avec le même nom
    protected function checkAndSetLabel($field, $message, $params)
    {
        // On réécrit la fonction
        return str_replace('{field}', '', $message);
    }
}
```