<?php

use App\Auth;
use App\Connection;
use App\HTML\Form;
use App\Model\Category;
use App\ObjectHelper;
use App\Table\CategoryTable;
use App\Validator;
use App\Validators\CategoryValidator;
use App\Validators\PostValidator;

Auth::check();

$success = false;
$errors = [];
$item = new Category();

if (!empty($_POST)) {
    $pdo = Connection::getPDO();
    $table = new CategoryTable($pdo);
    // Déclaration de la langue utilisée
    Validator::lang('fr');
    // On instancie Validator en vérifiant tout ce qui est envoyé en POST
    $v = new CategoryValidator($_POST, $table);
    // On change les éléments de la catégorie dans l'objet
    ObjectHelper::hydrate($item, $_POST, ['name', 'slug']);
    if ($v->validate()) {
        $table->create([
            'name' => $item->getName(),
            'slug' => $item->getSlug()
        ]);
        header('Location: ' . $router->url('admin_categories') . '?created=1');
        exit;
    } else { // Sinon, on renvoie les erreurs
        $errors = $v->errors();
    }
}
$form = new Form($item, $errors);
?>

<?php if ($success) : ?>
    <div class="alert alert-success">
        La catégorie a bien été enregistré
    </div>
<?php endif; ?>

<?php if (!empty($errors)) : ?>
    <div class="alert alert-danger">
        La catégorie n'a pas pu être enregistré. Merci de corriger vos erreurs.
    </div>
<?php endif; ?>

<h1>Créer un article</h1>

<?php require('_form.php') ?>