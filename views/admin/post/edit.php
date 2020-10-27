<?php

use App\Connection;
use App\HTML\Form;
use App\Model\Post;
use App\Table\PostTable;
use App\Validator;

$pdo = Connection::getPDO();
$postTable = new PostTable($pdo);

// On récupère l'article par son id
/** @var Post */
$post = $postTable->find($params['id']);

$success = false;
$errors = [];

if (!empty($_POST)) {
    // Déclaration de la langue utilisée
    Validator::lang('fr');
    // On instancie Validator en vérifiant tout ce qui est envoyé en POST
    $v = new Validator($_POST);
    // Vérification de la règle 'required' sur les champs 'name' et 'slug'
    $v->rule('required', ['name', 'slug']);
    // Vérification de la taille entre 3 et 200 sur les champs 'name' et 'slug'
    $v->rule('lengthBetween', ['name', 'slug'], 3, 200);
    // On change les éléments de l'article dans l'objet
    $post
        ->setName($_POST['name'])
        ->setContent($_POST['content'])
        ->setSlug($_POST['slug'])
        ->setCreatedAt($_POST['created_at']);
    // Si la validation des données est ok, on peut modifier l'article
    if ($v->validate()) {
        $postTable->update($post);
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

<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger">
        L'article n'a pas pu être modifié. Merci de corriger vos erreurs.
    </div>
<?php endif; ?>

<h1>Editer l'article <?= e($post->getName()) ?></h1>

<form action="" method="POST">
    <!-- Champ Titre -->
    <?= $form->input('name', 'Titre') ?>
    <!-- Champ URL -->
    <?= $form->input('slug', 'URL') ?>
    <!-- Champ Contenu -->
    <?= $form->textarea('content', 'Contenu') ?>
    <!-- Champ Date de Création -->
    <?= $form->input('created_at', 'Date de Création') ?>
    <button type="submit" class="btn btn-primary">Modifier</button>
</form>