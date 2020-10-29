<?php

use App\Auth;
use App\Connection;
use App\Model\Category;
use App\Table\CategoryTable;

Auth::check();

$title = 'Gestion des catégorie';
$pdo = Connection::getPDO();
/** @var Category[] */
$items = (new CategoryTable($pdo))->all();
// On récupère le lien acutel
$link = $router->url('admin_categories');
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
        <th>URL</th>
        <th>
            <a href="<?= $router->url('admin_category_new') ?>" class="btn btn-primary">
                Nouveau
            </a>
        </th>
    </thead>
    <tbody>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td>#<?= $item->getId() ?></td>
                <td>
                    <a href="<?= $router->url('admin_category', ['id' => $item->getId()]) ?>">
                        <?= e($item->getName()) ?>
                    </a>
                </td>
                <td><?= $item->getSlug() ?></td>
                <td>
                    <!-- Bouton pour éditer un article -->
                    <a href="<?= $router->url('admin_category', ['id' => $item->getId()]) ?>" class="btn btn-primary">
                        Editer
                    </a>
                    <!-- Formulaire + bouton pour supprimer un article -->
                    <form action="<?= $router->url('admin_category_delete', ['id' => $item->getId()]) ?>" style="display:inline" method="POST" onsubmit="return confirm('Voulez-vous vraiment effectuer cette action?')">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>