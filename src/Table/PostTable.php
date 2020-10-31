<?php

namespace App\Table;

use App\Model\Category;
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
            "SELECT * FROM {$this->table} ORDER BY created_at DESC",
            "SELECT COUNT(id) FROM {$this->table}",
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
                FROM {$this->table} p
                JOIN post_category pc ON pc.post_id = p.id
                WHERE pc.category_id = {$categoryID}
                ORDER BY created_at DESC",
            'SELECT COUNT(category_id) FROM post_category WHERE category_id = ' . $categoryID
        );

        $posts = $paginatedQuery->getItems(Post::class);
        (new CategoryTable($this->pdo))->hydratePosts($posts);
        return [$posts, $paginatedQuery];
    }

    /**
     * Met à jour les information d'un article en BDD, sinon renvoie une Exception
     *
     * @param Post $post L'article à modifier
     * @return void
     */
    public function updatePost(Post $post): void
    {
        $this->update([
            'name' => $post->getName(),
            'slug' => $post->getSlug(),
            'content' => $post->getContent(),
            'created_at' => $post->getCreatedAt()->format('Y-m-d H:i:s') // On converti au bon format en string
        ], $post->getId());
    }

    /**
     * Met à jour les information d'un article en BDD, sinon renvoie une Exception
     *
     * @param Post $post L'article à modifier
     * @return void
     */
    public function createPost(Post $post): void
    {
        $id = $this->create([
            'name' => $post->getName(),
            'slug' => $post->getSlug(),
            'content' => $post->getContent(),
            'created_at' => $post->getCreatedAt()->format('Y-m-d H:i:s') // On converti au bon format en string
        ]);
        $post->setId($id);
    }

    /**
     * Associe les bonnes catégories à un article
     *
     * @param int $id L'id de l'article
     * @param Category[] $categories La liste des catégories
     * @return void
     */
    public function attachCategories(int $id, array $categories)
    {
        $this->pdo->exec('DELETE FROM post_category WHERE post_id = ' . $id);
        $query = $this->pdo->prepare('INSERT INTO post_category SET post_id = ?, category_id = ?');
        foreach ($categories as $category) {
            $query->execute([$id, $category]);
        }
    }
}
