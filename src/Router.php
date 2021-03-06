<?php

namespace App;

use App\Security\ForbiddenException;

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
     * Fonction qui crée une route vers une page en méthode GET
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
     * Fonction qui crée une route vers une page en méthode POST
     *
     * @param string $url URL voulu
     * @param string $view chemin du fichier PHP à afficher
     * @param string|null $name nom de la page
     * @return self
     */
    public function post(string $url, string $view, ?string $name = null): self
    {
        $this->router->map('POST', $url, $view, $name);
        return $this;
    }

    /**
     * Fonction qui crée une route vers une page en méthode GET et POST
     *
     * @param string $url URL voulu
     * @param string $view chemin du fichier PHP à afficher
     * @param string|null $name nom de la page
     * @return self
     */
    public function match(string $url, string $view, ?string $name = null): self
    {
        $this->router->map('GET|POST', $url, $view, $name);
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
        // On récupère le chemin de la page souhaitée sinon page e404
        $view = $match['target'] ?: 'e404';
        // On sauvegarde les paramètres de l'URL (slug et id)
        $params = $match['params'];
        // On démarre le buffer
        $router = $this;
        $isAdmin = strpos($view, 'admin/') !== false;
        $layout = $isAdmin ? 'admin/layouts/default' : 'layouts/default';
        // On essaie d'accéder aux pages
        try {
            ob_start();
            require $this->viewPath . DIRECTORY_SEPARATOR . $view . '.php';
            // On récupère le contenu dans le buffer et on le vide
            $content = ob_get_clean();
            // On charge le layout de la page HTML qui appelle $content à l'intérieur
            require $this->viewPath . DIRECTORY_SEPARATOR . $layout . '.php';
        } catch (ForbiddenException $e) {
            // Si on reçoit une ForbiddenException (pages admin), renvoie vers la page de login
            header('Location: ' . $this->url('login') . '?forbidden=1');
        }

        return $this;
    }

    public function url(string $name, array $params = [])
    {
        return $this->router->generate($name, $params);
    }
}
