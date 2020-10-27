<?php

namespace App\Table;

use App\Model\Post;
use App\PaginatedQuery;

final class PostTable extends Table
{
    protected $table = "post";
    protected $class = Post::class;

    public function delete(int $id): void
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $ok = $query->execute([$id]);
        if (!$ok)
            throw new \Exception("Impossible de supprimer l'enregristrement $id dans la table {$this->table}");
    }

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
    public function update(Post $post): void
    {
        $query = $this->pdo->prepare("UPDATE {$this->table} SET name = :name, slug = :slug, created_at = :created, content = :content WHERE id = :id");
        $ok = $query->execute([
            'id' => $post->getId(),
            'name' => $post->getName(),
            'slug' => $post->getSlug(),
            'content' => $post->getContent(),
            'created' => $post->getCreatedAt()->format('Y-m-d H:i:s') // On converti au bon format en string
        ]);
        if (!$ok)
            throw new \Exception("Impossible de modifier l'enregristrement {$post->getId()} dans la table {$this->table}");
    }
}
