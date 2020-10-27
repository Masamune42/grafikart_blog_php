<?php

use App\Connection;
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
    // Vérification de la règle 'required' sur le champ 'name'
    $v->rule('required', 'name');
    // Vérification de la taille entre 3 et 200 sur le champ 'name'
    $v->rule('lengthBetween', 'name', 3, 200);
    // On change le nom de l'article dans l'objet
    $post->setName($_POST['name']);
    // Si la validation des données est ok, on peut modifier l'article
    if ($v->validate()) {
        $postTable->update($post);
        $success = true;
    } else { // Sinon, on renvoie les erreurs
        $errors = $v->errors();
    }
}
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
    <div class="form-group">
        <label for="name">Titre</label>
        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" name="name" value="<?= e($post->getName()) ?>" required>
        <?php if (isset($errors['name'])) : ?>
            <!-- Message d'erreur si le champ n'a pas passé les validations -->
            <div class="invalid-feedback">
                <?= implode('<br>', $errors['name']) ?>
            </div>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Modifier</button>
</form>