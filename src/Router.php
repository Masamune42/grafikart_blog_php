<?php

namespace App;

class Router
{

    /**
     * @var string
     */
    private $viewPath;

    /**
     * @var AltoRouter
     */
    protected $router;

    /**
     * Contructeur
     *
     * @param string $viewPath
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
        $this->router = new \AltoRouter();
    }

    /**
     * Fonction qui crée une route vers une page
     *
     * @param string $url URL voulu
     * @param string $view chemin du fichier PHP à afficher
     * @param string|null $name nom de la page
     * @return self
     */
    public function get(string $url, string $view, ?string $name = null): self
    {
        $this->router->map('GET', $url, $view, $name);
        return $this;
    }

    /**
     * Fonction qui récupère la route créée et renvoie vers la bonne target
     *
     * @return void
     */
    public function run()
    {
        // On récupère les infos de l'adresse URL courante et elle match avec une existante
        $match = $this->router->match();
        // On récupère le chemin de la page souhaitée
        $view = $match['target'];
        // On démarre le buffer
        ob_start();
        require $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';
        // On récupère le contenu dans le buffer et on le vide
        $content = ob_get_clean();
        // On charge le layout de la page HTML qui appelle $content à l'intérieur
        require $this->viewPath . DIRECTORY_SEPARATOR . 'layouts/default.php';
        return $this;
    }
}
