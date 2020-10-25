<?php
// 1ère méthode pour remplir le tableau des catégories
// $categories = [];
// // On rempli un tableau avec le lien HTML de toutes les catégories du post
// foreach ($post->getCategories() as $category) {
//     $url = $router->url('category', ['id' => $category->getId(), 'slug' => $category->getSlug()]);
//     $categories[] = <<<HTML
//     <a href="{$url}">{$category->getName()}</a>
// HTML;

// 2e méthode
$categories = array_map(function ($category) use($router) {
    $url = $router->url('category', ['id' => $category->getId(), 'slug' => $category->getSlug()]);
    return <<<HTML
    <a href="{$url}">{$category->getName()}</a>
HTML;
}, $post->getCategories());

?>
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title"><?= htmlentities($post->getName()) ?></h5>
        <p class="text-muted">
            <?= $post->getCreated_at()->format('d F Y H:i') ?>
            <?php if(!empty($post->getCategories())) : ?>
            ::
            <?= implode(', ', $categories) ?>
            <?php endif; ?>
        </p>
        <p><?= $post->getExcerpt() ?></p>
        <p>
            <a href="<?= $router->url('post', ['id' => $post->getId(), 'slug' => $post->getSlug()]) ?>" class="btn btn-primary">Voir plus</a>
        </p>
    </div>
</div>