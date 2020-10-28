<?php

use App\Auth;
use App\Connection;
use App\Table\PostTable;

Auth::check();

$title = 'Administration';
$pdo = Connection::getPDO();
[$posts, $pagination] = (new PostTable($pdo))->findPaginated();
// On récupère le lien acutel
$link = $router->url('admin_posts');
?>

<?php if (isset($_GET['delete'])) : ?>
    <div class="alert alert-success">
        L'enregistrement a bien été supprimé
    </div>
<?php endif; ?>
<table class="table">
    <thead>
        <th>ID</th>
        <th>Titre</th>
        <th>
            <a href="<?= $router->url('admin_post_new') ?>" class="btn btn-primary">
                Nouveau
            </a>
        </th>
    </thead>
    <tbody>
        <?php foreach ($posts as $post) : ?>
            <tr>
                <td>#<?= $post->getId() ?></td>
                <td>
                    <a href="<?= $router->url('admin_post', ['id' => $post->getId()]) ?>">
                        <?= e($post->getName()) ?>
                    </a>
                </td>
                <td>
                    <!-- Bouton pour éditer un article -->
                    <a href="<?= $router->url('admin_post', ['id' => $post->getId()]) ?>" class="btn btn-primary">
                        Editer
                    </a>
                    <!-- Formulaire + bouton pour supprimer un article -->
                    <form action="<?= $router->url('admin_post_delete', ['id' => $post->getId()]) ?>" style="display:inline" method="POST" onsubmit="return confirm('Voulez-vous vraiment effectuer cette action?')">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Affichage des boutons page précédente / suivante -->
<div class="d-flex justify-content-between my-4">
    <?= $pagination->previousLink($link); ?>
    <?= $pagination->nextLink($link); ?>
</div>