<?php

namespace App\Table;

use App\Model\Post;
use App\PaginatedQuery;

final class PostTable extends Table
{
    protected $table = "post";
    protected $class = Post::class;

    /**
     * Retourne dans un tableau les articles de la page et la requête SQL de pagination pour la page courante
     *
     * @return array tableau les articles de la page et la requête SQL de pagination pour la page courante
     */
    public function findPaginated()
    {
        $paginatedQuery = new PaginatedQuery(
            "SELECT * FROM post ORDER BY created_at DESC",
            'SELECT COUNT(id) FROM post',
            $this->pdo
        );

        $posts = $paginatedQuery->getItems(Post::class);
        (new CategoryTable($this->pdo))->hydratePosts($posts);
        return [$posts, $paginatedQuery];
    }

    /**
     * Retourne dans un tableau les articles de la page et la requête SQL de pagination pour l'id de catégorie donné
     *
     * @param integer $categoryID ID de la catégorie
     * @return array Tableau contenant les articles de la page et la requête SQL de pagination
     */
    public function findPaginatedForCategory(int $categoryID)
    {
        $paginatedQuery = new PaginatedQuery(
            "SELECT p.*
                FROM post p
                JOIN post_category pc ON pc.post_id = p.id
                WHERE pc.category_id = {$categoryID}
                ORDER BY created_at DESC",
            'SELECT COUNT(category_id) FROM post_category WHERE category_id = ' . $categoryID
        );

        $posts = $paginatedQuery->getItems(Post::class);
        (new CategoryTable($this->pdo))->hydratePosts($posts);
        return [$posts, $paginatedQuery];
    }
}
