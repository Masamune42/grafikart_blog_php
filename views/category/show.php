<?php

use App\Connection;
use App\Model\Category;
use App\Model\Post;
use App\URL;

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

$currentPage = URL::getPositiveInt('page', 1);
if ($currentPage <= 0) {
    throw new Exception('Numéro de page invalide');
}

// On récupère le nombre de catégorie avec l'id courant associés à des articles
$count = (int)$pdo
    ->query('SELECT COUNT(category_id) FROM post_category WHERE category_id = ' . $category->getId())
    ->fetch(PDO::FETCH_NUM)[0];
// Nombre limite d'articles par page
$perPage = 12;
$pages = ceil($count / $perPage);
if ($currentPage > $pages) {
    throw new Exception('Cette page n\'existe pas');
}

$offset = $perPage * ($currentPage - 1);
// Requête avec jointure pour récupérer les articles dont le numéro de catégorie est celui sélectionné
$query = $pdo->query("
    SELECT p.*
    FROM post p
    JOIN post_category pc ON pc.post_id = p.id
    WHERE pc.category_id = {$category->getId()}
    ORDER BY created_at DESC
    LIMIT $perPage OFFSET $offset
    ");
$posts = $query->fetchAll(PDO::FETCH_CLASS, Post::class);
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
    <?php if ($currentPage > 1) : ?>
        <?php
        $l = $link;
        if ($currentPage > 2) $l .= '?page=' . ($currentPage - 1);
        ?>
        <a href="<?= $l ?>" class="btn btn-primary">&laquo; Page précédente</a>
    <?php endif; ?>
    <?php if ($currentPage < $pages) : ?>
        <a href="<?= $link ?>?page=<?= $currentPage + 1 ?>" class="btn btn-primary ml-auto">Page suivante &raquo;</a>
    <?php endif; ?>
</div>