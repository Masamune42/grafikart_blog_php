<form action="" method="POST">
    <!-- Champ Titre -->
    <?= $form->input('name', 'Titre') ?>
    <!-- Champ URL -->
    <?= $form->input('slug', 'URL') ?>
    <!-- Champ catégorie -->
    <?= $form->select('categories_ids', 'Catégories', $categories) ?>
    <!-- Champ Contenu -->
    <?= $form->textarea('content', 'Contenu') ?>
    <!-- Champ Date de Création -->
    <?= $form->input('created_at', 'Date de Création') ?>
    <button type="submit" class="btn btn-primary">
        <?php if ($post->getId() !== null) : ?>
            Modifier
        <?php else : ?>
            Créer
        <?php endif; ?>
    </button>
</form>