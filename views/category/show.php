<?php

use App\Connection;
use App\Table\CategoryTable;
use App\Table\PostTable;

// On récupère l'id et le slug
$id = (int)$params['id'];
$slug = $params['slug'];
// On se connecte à la BDD et on récupère la catégorie de l'id
$pdo = Connection::getPDO();
// On récupère la catégorie courante avec son ID
$category = (new CategoryTable($pdo))->find($id);

// Si le slug que l'on a tapé est différent de celui de la catégorie dont l'id est bon
if ($category->getSlug() !== $slug) {
    // On génère le vrai URL de la catégorie
    $url = $router->url('category', ['slug' => $category->getSlug(), 'id' => $id]);
    // Redirection vers la bonne URL
    http_response_code(301);
    header('Location: ' . $url);
}

/*  */
/* Listing des articles ayant la même catégorie */
/*  */

/** AIDE POUR CREER LES FONCTIONS
 * Paramètres qui varient :
 * $sqlListing: string
 * $classMapping: string
 * $sqlCount: string
 * $perPage: int = 12
 * 
 * Paramètre externes :
 * //$currentPage
 * $pdo: PDO = Conection::getPDO()
 * 
 * Fonctions :
 * getItems():array -> va réupérer les résultats / articles pour notre listing
 * previousPageLink(): ?string -> On récupère le lien de la page précédente
 * nextPageLink(): ?string -> On récupère le lien de la page suivante
 */
// On récupère les articles associés à la catégorie et l'objet pagination
[$posts, $paginatedQuery] = (new PostTable($pdo))->findPaginatedForCategory($category->getId());
// On récupère le lien acutel
$link = $router->url('category', ['slug' => $category->getSlug(), 'id' => $id]);
$title = "Catégorie {$category->getName()}";
?>

<h1>Catégorie <?= e($category->getName()) ?></h1>

<!-- Affiche des articles -->
<div class="row">
    <?php foreach ($posts as $post) : ?>
        <div class="col-md-3">
            <?php require dirname(__DIR__) . '/post/card.php' ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Affichage des boutons page précédente / suivante -->
<div class="d-flex justify-content-between my-4">
    <?= $paginatedQuery->previousLink($link); ?>
    <?= $paginatedQuery->nextLink($link); ?>
</div>