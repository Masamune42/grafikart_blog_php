<?php

use App\Connection;
use App\HTML\Form;
use App\Model\Post;
use App\ObjectHelper;
use App\Table\PostTable;
use App\Validator;
use App\Validators\PostValidator;



$success = false;
$errors = [];
$post = new Post();

if (!empty($_POST)) {
    $pdo = Connection::getPDO();
    $postTable = new PostTable($pdo);
    // Déclaration de la langue utilisée
    Validator::lang('fr');
    // On instancie Validator en vérifiant tout ce qui est envoyé en POST
    $v = new PostValidator($_POST, $postTable, $post->getId());
    // On change les éléments de l'article dans l'objet
    ObjectHelper::hydrate($post, $_POST, ['name', 'content', 'slug', 'created_at']);
    if ($v->validate()) {
        $postTable->create($post);
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