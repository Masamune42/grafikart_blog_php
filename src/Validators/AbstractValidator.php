<?php

namespace App\Validators;

use App\Validator;

abstract class AbstractValidator
{
    protected $data;

    protected $validator;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->validator = new Validator($this->data);
    }

    /**
     * Fonction qui valide le format des données qu'on lui passe
     *
     * @return boolean true si les données sont validées, sinon false
     */
    public function validate(): bool
    {
        return $this->validator->validate();
    }

    /**
     * Fonction qui renvoie le tableau des erreurs détectées
     *
     * @return array Le tableau des erreurs détectées
     */
    public function errors(): array
    {
        return $this->validator->errors();
    }
}
