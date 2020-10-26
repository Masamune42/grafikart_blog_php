<?php

use App\Connection;
use App\Table\PostTable;

$title = 'Mon blog';

$pdo = Connection::getPDO();
// On déclare un nouvel objet avec lequel on va pouvoir effectuer des requêtes sur les articles
$table = new PostTable($pdo);

// OLD : récupération du tableau dans des variables
/*
$var = $table->findPaginated();
$posts = $var[0];
$pagination = $var[1];
*/
// On récupère les objets d'un tableau dans des variables définies
/* 
list($posts, $pagination) = $table->findPaginated();
 */
// 2e solution : seulement dans les nouvelles versions de PHP
[$posts, $pagination] = $table->findPaginated();

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
    <?= $pagination->previousLink($link); ?>
    <?= $pagination->nextLink($link); ?>
</div>