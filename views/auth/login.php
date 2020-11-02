<?php

use App\Connection;
use App\HTML\Form;
use App\Model\User;
use App\Table\Exception\NotFoundException;
use App\Table\UserTable;

$user = new User();
$errors = [];
// Si on soumet le formulaire
if (!empty($_POST)) {
    $user->setUsername($_POST['username']);
    // On initialise les erreurs
    $errors['password'] = 'Identifiants ou mot de passe incorrect';
    // Si le champs username et password ne sont pas vides, vérification du login / mdp
    if (!empty($_POST['username']) || !empty($_POST['password'])) {
        /** @var UserTable */
        $table = new UserTable(Connection::getPDO());
        try {
            // On cherche l'utilisateur en BDD
            /** @var User */
            $u = $table->findByUsername($_POST['username']);
            // Si le mot de passe est bon, on démarre la session et on redirige vers admin_posts
            if (password_verify($_POST['password'], $u->getPassword()) === true) {
                session_start();
                $_SESSION['auth'] = $u->getId();
                header('Location: ' . $router->url('admin_posts'));
                exit;
            }
        } catch (NotFoundException $e) {
            // Si l'utilisateur n'est pas trouvé, renvoie une NotFoundException que l'on catch
        }
    }
}

$form = new Form($user, $errors);
?>
<h1>Se connecter</h1>

<?php if (isset($_GET['forbidden'])) : ?>
    <div class="alert alert-danger">
        Vous ne pouvez pas accéder à cette page
    </div>
<?php endif; ?>

<form action="<?= $router->url('login') ?>" method="POST">
    <?= $form->input('username', 'Nom d\'utilisateur') ?>
    <?= $form->input('password', 'Mot de passe') ?>
    <button class="btn btn-primary">Se connecter</button>
</form>