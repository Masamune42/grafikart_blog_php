<?php

use App\Helpers\Text;
use App\Model\Post;

$title = 'Mon blog';

$pdo = new PDO('mysql:dbname=tutoblog;host=127.0.0.1', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
$query = $pdo->query('SELECT * FROM post ORDER BY created_at DESC LIMIT 12');
$posts = $query->fetchAll(PDO::FETCH_CLASS, Post::class);
?>

<h1>Mon blog</h1>

<div class="row">
    <?php foreach ($posts as $post) : ?>
        <div class="col-md-3">
           <?php require 'card.php' ?>
        </div>
    <?php endforeach; ?>
</div>