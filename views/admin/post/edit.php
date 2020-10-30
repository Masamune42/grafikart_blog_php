<?php

use App\Auth;
use App\Connection;
use App\HTML\Form;
use App\Model\Post;
use App\ObjectHelper;
use App\Table\CategoryTable;
use App\Table\PostTable;
use App\Validator;
use App\Validators\PostValidator;

Auth::check();

$pdo = Connection::getPDO();
$postTable = new PostTable($pdo);
$categoryTable = new CategoryTable($pdo);
$categories = $categoryTable->list();
// On récupère l'article par son id
/** @var Post */
$post = $postTable->find($params['id']);
$categoryTable->hydratePosts([$post]);

$success = false;
$errors = [];

if (!empty($_POST)) {
    // Déclaration de la langue utilisée
    Validator::lang('fr');
    // On instancie Validator en vérifiant tout ce qui est envoyé en POST
    $v = new PostValidator($_POST, $postTable, $post->getId());
    // On change les éléments de l'article dans l'objet
    ObjectHelper::hydrate($post, $_POST, ['name', 'content', 'slug', 'created_at']);
    if ($v->validate()) {
        $postTable->updatePost($post);
        $success = true;
    } else { // Sinon, on renvoie les erreurs
        $errors = $v->errors();
    }
}
$form = new Form($post, $errors);
?>

<?php if ($success) : ?>
    <div class="alert alert-success">
        L'article a bien été modifié
    </div>
<?php endif; ?>

<?php if (isset($_GET['created'])) : ?>
    <div class="alert alert-success">
        L'article a bien été créé
    </div>
<?php endif; ?>

<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger">
        L'article n'a pas pu être modifié. Merci de corriger vos erreurs.
    </div>
<?php endif; ?>

<h1>Editer l'article <?= e($post->getName()) ?></h1>

<?php require('_form.php') ?>