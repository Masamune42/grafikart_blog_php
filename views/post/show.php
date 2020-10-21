<?php

use App\Connection;
use App\Model\{Category, Post};

// On récupère l'id et le slug
$id = (int)$params['id'];
$slug = $params['slug'];
// On se connecte à la BDD et on récupère le post de l'id
$pdo = Connection::getPDO();
$query = $pdo->prepare('SELECT * FROM post WHERE id = :id');
$query->execute(['id' => $id]);
$query->setFetchMode(PDO::FETCH_CLASS, Post::class);
/** @var Post|false */
$post = $query->fetch();

if ($post === false) {
    throw new Exception('Aucun article ne correspond à cet ID');
}

// Si le slug que l'on a tapé est différent de celui du post dont l'id est bon
if ($post->getSlug() !== $slug) {
    // On génère le vrai URL de l'article
    $url = $router->url('post', ['slug' => $post->getSlug(), 'id' => $id]);
    // Redirection vers la bonne URL
    http_response_code(301);
    header('Location: ' . $url);
}

$query = $pdo->prepare('
SELECT c.id, c.slug, c.name
FROM post_category pc
JOIN category c ON pc.category_id = c.id
WHERE pc.post_id = :id');
$query->execute(['id' => $post->getId()]);
$query->setFetchMode(PDO::FETCH_CLASS, Category::class);
/** @var Category[] */
$categories = $query->fetchAll();

$title = "Article {$post->getName()}";
?>

<h1 class="card-title"><?= e($post->getName()) ?></h1>
<p class="text-muted"><?= $post->getCreated_at()->format('d F Y H:i') ?></p>
<?php foreach ($categories as $k => $category) : ?>
    <?php if ($k > 0) : ?>
        ,
    <?php endif; ?>
    <a href="<?= $router->url('category', ['id' => $category->getId(), 'slug' => $category->getSlug()]) ?>"><?= e($category->getName()) ?></a>
<?php endforeach; ?>
<p><?= $post->getFormattedContent() ?></p>