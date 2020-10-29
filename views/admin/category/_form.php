<form action="" method="POST">
    <!-- Champ Titre -->
    <?= $form->input('name', 'Titre') ?>
    <!-- Champ URL -->
    <?= $form->input('slug', 'URL') ?>
    <button type="submit" class="btn btn-primary">
        <?php if ($item->getId() !== null) : ?>
            Modifier
        <?php else : ?>
            Créer
        <?php endif; ?>
    </button>
</form>