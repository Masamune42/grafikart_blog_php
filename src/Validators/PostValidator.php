<?php

namespace App\Validators;

use App\Table\PostTable;

class PostValidator extends AbstractValidator
{
    public function __construct(array $data, PostTable $table, ?int $postID = null, array $categoriesIds)
    {
        parent::__construct($data);
        // Vérification de la règle 'required' sur les champs 'name' et 'slug'
        $this->validator->rule('required', ['name', 'slug']);
        // Vérification de la taille entre 3 et 200 sur les champs 'name' et 'slug'
        $this->validator->rule('lengthBetween', ['name', 'slug'], 3, 200);
        // On vérifie que le slug est au format d'un slug
        $this->validator->rule('slug', 'slug');
        // On vérifie que les ids des catégories passés dans le formulaire (param 2), existent dans la liste des catégories existantes (param 3)
        $this->validator->rule('subset','categories_ids', array_keys($categoriesIds));
        $this->validator->rule(function ($field, $value) use ($table, $postID) {
            return !$table->exists($field, $value, $postID);
        }, ['slug', 'name'], 'Cette valeur est déjà utilisée');
    }
}
