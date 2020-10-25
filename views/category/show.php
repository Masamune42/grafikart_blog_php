<?php

use App\Connection;
use App\Model\Category;
use App\Model\Post;
use App\PaginatedQuery;

// On récupère l'id et le slug
$id = (int)$params['id'];
$slug = $params['slug'];
// On se connecte à la BDD et on récupère la catégorie de l'id
$pdo = Connection::getPDO();
$query = $pdo->prepare('SELECT * FROM category WHERE id = :id');
$query->execute(['id' => $id]);
$query->setFetchMode(PDO::FETCH_CLASS, Category::class);
/** @var Category|false */
$category = $query->fetch();

if ($category === false) {
    throw new Exception('Aucune catégorie ne correspond à cet ID');
}

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

$paginatedQuery = new PaginatedQuery(
    "SELECT p.*
        FROM post p
        JOIN post_category pc ON pc.post_id = p.id
        WHERE pc.category_id = {$category->getId()}
        ORDER BY created_at DESC",
    'SELECT COUNT(category_id) FROM post_category WHERE category_id = ' . $category->getId()
);

/** @var Post[] */
$posts = $paginatedQuery->getItems(Post::class);
$link = $router->url('category', ['slug' => $category->getSlug(), 'id' => $id]);
/* Partie récupération des catégories des articles */
$postsByID = [];
// On récupère l'id de chaque post et on y place chaque post correspondant
foreach ($posts as $post) {
    $postsByID[$post->getId()] = $post;
}
/** @var Category[] */
$categories = $pdo
    ->query('SELECT c.*, pc.post_id
            FROM post_category pc
            JOIN category c ON c.id = pc.category_id 
            WHERE pc.post_id IN (' . implode(",", array_keys($postsByID)) . ')')
    ->fetchAll(PDO::FETCH_CLASS, Category::class);


// On va insérer chaque catégorie dans le tableau de catégories du post dont l'id de catégorie courant est le même
foreach ($categories as $category) {
    $postsByID[$category->getPost_id()]->addCategory($category);
}

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