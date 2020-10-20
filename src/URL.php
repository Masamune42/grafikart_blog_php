<?php

namespace App;

class URL
{
    /**
     * Fonction qui détecte si le paramètre d'url donné est un entier, sinon renvoie une exception
     *
     * @param string $name Nom du paramètre de l'url a vérifier
     * @param integer|null $default Valeur par défaut à renvoyer si le paramètre n'est pas défini dans l'url
     * @return integer|null Paramètre de l'url converti en entier
     */
    public static function getInt(string $name, ?int $default): ?int
    {
        // Si on ne repère pas le paramètre de l'url rentré, on retourne la valeur par défaut
        // ex : si on n'a pas le paramètre page de renseigné dans l'url, on renvoie 1 (page 1)
        if (!isset($_GET[$name])) return $default;

        if($_GET[$name] === '0') return 0;

        // On vérifie que le paramètre de la page est un entier, sinon on renvoie une exception
        if (!filter_var($_GET[$name], FILTER_VALIDATE_INT)) {
            throw new \Exception("Le paramètre $name dans l'url n'est pas un entier");
        }

        return (int)$_GET[$name];
    }

    /**
     * Fonction qui renvoie le paramètre s'il est positif, sinon une Exception
     *
     * @param string $name Nom du paramètre de l'url a vérifier
     * @param integer|null $default Valeur par défaut à renvoyer si le paramètre n'est pas défini dans l'url
     * @return integer|null Paramètre de l'url
     */
    public static function getPositiveInt(string $name, ?int $default = null): ?int
    {
        $param = self::getInt($name, $default);
        if($param !== null && $param <= 0) {
            throw new \Exception("Le paramètre $name dans l'url n'est pas un entier positif");
        }
        return $param;
    }
}
