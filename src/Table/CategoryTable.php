<?php

namespace App\Table;

use App\Model\Category;
use PDO;

final class CategoryTable extends Table
{
    protected $table = "category";
    protected $class = Category::class;

    /**
     * Fonction qui permet d'associer les catégories aux articles envoyés en paramètre
     *
     * @param App\Model\Post[] $posts
     * @return void
     */
    public function hydratePosts(array $posts): void
    {
        /* Partie récupération des catégories des articles */
        $postsByID = [];
        // On récupère l'id de chaque post et on y place chaque post correspondant
        foreach ($posts as $post) {
            $postsByID[$post->getId()] = $post;
        }
        /** @var Category[] */
        $categories = $this->pdo
            ->query('SELECT c.*, pc.post_id
             FROM post_category pc
             JOIN category c ON c.id = pc.category_id 
             WHERE pc.post_id IN (' . implode(",", array_keys($postsByID)) . ')')
            ->fetchAll(PDO::FETCH_CLASS, Category::class);
        // On va récupérer toutes les informations d'un article ainsi que l'id de l'article associé
        // Plusieurs articles pour une même catégorie = plusieurs lignes récupérées = plusieurs cases du tableau

        // On va ajouter chaque catégorie dans l'article correspondant
        // Pour cela on récupère l'id du poste de la catégorie (récupéré dans la requête SQL) et on fait correspondre à l'id de l'article
        // Changer l'article dans ce tableau = changer l'article PARTOUT car même adresse (= objet)
        foreach ($categories as $category) {
            $postsByID[$category->getPost_id()]->addCategory($category);
        }
    }

    /**
     * Fonction qui récupère tous les éléments de la table courante
     *
     * @return array Tableau des éléments de la table
     */
    public function all(): array
    {
        return $this->queryAndFetchAll("SELECT * FROM {$this->table} ORDER BY id DESC");
    }

    public function list(): array
    {
        /** @var Category[] */
        $categories = $this->queryAndFetchAll("SELECT * FROM {$this->table} ORDER BY name ASC");
        $results = [];
        foreach($categories as $category) {
            $results[$category->getId()] = $category->getName();
        }
        return $results;
    }
}
