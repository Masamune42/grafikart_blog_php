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

$success = false;
$errors = [];
$post = new Post();
$pdo = Connection::getPDO();
$categoryTable = new CategoryTable($pdo);
$categories = $categoryTable->list();

if (!empty($_POST)) {
    $postTable = new PostTable($pdo);
    // On instancie Validator en vérifiant tout ce qui est envoyé en POST
    $v = new PostValidator($_POST, $postTable, $post->getId(), $categories);
    // On change les éléments de l'article dans l'objet
    ObjectHelper::hydrate($post, $_POST, ['name', 'content', 'slug', 'created_at']);
    if ($v->validate()) {
        // Début de la transaction
        $pdo->beginTransaction();
        $postTable->createPost($post);
        $postTable->attachCategories($post->getId(), $_POST['categories_ids']);
        // Fin de la transaction
        $pdo->commit();
        header('Location: '. $router->url('admin_post', ['id' => $post->getId()]). '?created=1');
        exit;
    } else { // Sinon, on renvoie les erreurs
        $errors = $v->errors();
    }
}
$form = new Form($post, $errors);
?>

<?php if ($success) : ?>
    <div class="alert alert-success">
        L'article a bien été enregistré
    </div>
<?php endif; ?>

<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger">
        L'article n'a pas pu être enregistré. Merci de corriger vos erreurs.
    </div>
<?php endif; ?>

<h1>Créer un article</h1>

<?php require('_form.php') ?>