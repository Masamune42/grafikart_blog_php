<?php

namespace App\Validators;

use App\Table\CategoryTable;

class CategoryValidator extends AbstractValidator
{
    public function __construct(array $data, CategoryTable $table, ?int $id = null)
    {
        parent::__construct($data);
        // Vérification de la règle 'required' sur les champs 'name' et 'slug'
        $this->validator->rule('required', ['name', 'slug']);
        // Vérification de la taille entre 3 et 200 sur les champs 'name' et 'slug'
        $this->validator->rule('lengthBetween', ['name', 'slug'], 3, 200);
        // On vérifie que le slug est au format d'un slug
        $this->validator->rule('slug', 'slug');
        $this->validator->rule(function ($field, $value) use ($table, $id) {
            return !$table->exists($field, $value, $id);
        }, ['slug', 'name'], 'Cette valeur est déjà utilisée');
    }
}
