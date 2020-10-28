<?php

namespace App\HTML;

use App\Model\Post;
use DateTimeInterface;

class Form
{
    /**
     * @var Post
     */
    private $data;

    private $errors;

    public function __construct($data, array $errors)
    {
        $this->data = $data;
        $this->errors = $errors;
    }

    /**
     * Fonction qui crée un champ input en HTML
     *
     * @param string $key La clé qui va aussi correspondre au nom
     * @param string $label Le label à afficher pour le champ
     * @return string Le code HTML du champ input
     */
    public function input(string $key, string $label): string
    {
        $value = $this->getValue($key);
        return <<<HTML
        <div class="form-group">
            <label for="field{$key}">{$label}</label>
            <input type="text" id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}" value="{$value}" required>
            {$this->getInvalidFeedack($key)}
        </div>
HTML;
    }

    /**
     * Fonction qui crée un champ textarea en HTML
     *
     * @param string $key La clé qui va aussi correspondre au nom
     * @param string $label Le label à afficher pour le champ
     * @return string Le code HTML du champ textarea
     */
    public function textarea(string $key, string $label): string
    {
        $value = $this->getValue($key);
        return <<<HTML
        <div class="form-group">
            <label for="field{$key}">{$label}</label>
            <textarea type="text" id="field{$key}" class="{$this->getInputClass($key)}" name="{$key}" required>{$value}</textarea>
            {$this->getInvalidFeedack($key)}
        </div>
HTML;
    }

    /**
     * Renvoie la valeur associé à la clé du champ à récupérer
     *
     * @param string $key La clé du champ
     * @return string La valeur associée à l'objet du champ
     */
    private function getValue(string $key): ?string
    {
        if (is_array($this->data)) {
            return $this->data[$key] ?? null;
        }
        $method = 'get' . str_replace(' ','',ucwords(str_replace('_',' ',$key)));
        $value = $this->data->$method();
        if($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        return $value;
    }

    /**
     * Fonction qui renvoie la classe d'un champ et de définir s'il est invalide
     *
     * @param string $key La clé du champ
     * @return string La classe du champ
     */
    private function getInputClass(string $key): string
    {
        $inputClass = 'form-control';
        if (isset($this->errors[$key])) {
            $inputClass .= ' is-invalid';
        }
        return $inputClass;
    }

    /**
     * Fonction qui renvoie le code HTML du message d'erreur sous le champ s'il y a une erreur, sinon un string vide
     *
     * @param string $key La clé du champ
     * @return string La classe du champe code HTML du message
     */
    private function getInvalidFeedack(string $key): string
    {
        if (isset($this->errors[$key])) {
            return '<div class="invalid-feedback">' . implode('<br>', $this->errors[$key]) . '</div>';
        }
        return '';
    }
}
