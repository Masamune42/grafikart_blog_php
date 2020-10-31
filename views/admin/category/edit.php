<?php

use App\Auth;
use App\Connection;
use App\HTML\Form;
use App\Model\Post;
use App\ObjectHelper;
use App\Table\CategoryTable;
use App\Validator;
use App\Validators\CategoryValidator;

Auth::check();

$pdo = Connection::getPDO();
$table = new CategoryTable($pdo);

// On récupère l'article par son id
/** @var Post */
$item = $table->find($params['id']);

$success = false;
$errors = [];

$fields = ['name', 'slug'];

if (!empty($_POST)) {
    // On instancie Validator en vérifiant tout ce qui est envoyé en POST
    $v = new CategoryValidator($_POST, $table, $item->getId());
    // On change les éléments de l'article dans l'objet
    ObjectHelper::hydrate($item, $_POST, $fields);
    if ($v->validate()) {
        $table->update([
            'name' => $item->getName(),
            'slug' => $item->getSlug()
        ], $item->getId());
        $success = true;
    } else { // Sinon, on renvoie les erreurs
        $errors = $v->errors();
    }
}
$form = new Form($item, $errors);
?>

<?php if ($success) : ?>
    <div class="alert alert-success">
        La catégorie a bien été modifiée
    </div>
<?php endif; ?>

<?php if (isset($_GET['created'])) : ?>
    <div class="alert alert-success">
        La catégorie a bien été créée
    </div>
<?php endif; ?>

<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger">
        La catégorie n'a pas pu être modifiée. Merci de corriger vos erreurs.
    </div>
<?php endif; ?>

<h1>Editer la catégorie <?= e($item->getName()) ?></h1>

<?php require('_form.php') ?>