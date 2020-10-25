<?php

use App\Connection;
use App\Model\Category;
use App\Model\Post;
use App\PaginatedQuery;

$title = 'Mon blog';

$pdo = Connection::getPDO();


$paginatedQuery = new PaginatedQuery(
    "SELECT * FROM post ORDER BY created_at DESC",
    'SELECT COUNT(id) FROM post'
);

/** @var Post[] */
$posts = $paginatedQuery->getItems(Post::class);

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

$link = $router->url('home');
?>

<h1>Mon blog</h1>

<div class="row">
    <?php foreach ($posts as $post) : ?>
        <div class="col-md-3">
            <?php require 'card.php' ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="d-flex justify-content-between my-4">
    <?= $paginatedQuery->previousLink($link); ?>
    <?= $paginatedQuery->nextLink($link); ?>
</div>