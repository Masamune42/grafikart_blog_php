<?php

namespace App\Table;

use App\Table\Exception\NotFoundException;
use PDO;

abstract class Table
{
    protected $pdo;

    protected $table = null;

    protected $class = null;

    public function __construct(PDO $pdo)
    {
        if ($this->table === null) {
            throw new \Exception("La class " . get_class($this) . " n'a pas de propriété \$table");
        }
        if ($this->class === null) {
            throw new \Exception("La class " . get_class($this) . " n'a pas de propriété \$class");
        }
        $this->pdo = $pdo;
    }

    /**
     * Récupération des information d'un élément suivant son ID
     *
     * @param integer $id ID de l'élément
     * @return void l'élément avec l'ID recherché
     */
    public function find(int $id)
    {
        $query = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE id = :id');
        $query->execute(['id' => $id]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        /** @var Category|false */
        $result = $query->fetch();
        if ($result === false) {
            throw new NotFoundException($this->table, $id);
        }
        return $result;
    }

    /**
     * Vérifie si une valeur existe dans la table
     *
     * @param string $field Champ à rechercher
     * @param mixed $value Valeur associée au champ
     * @return boolean true s'il y a déjà une valeur qui existe, sinon false
     */
    public function exists(string $field, $value, ?int $except = null): bool
    {
        $sql = "SELECT count(id) FROM {$this->table} WHERE $field = ?";
        $params = [$value];
        if ($except !== null) {
            $sql .= " AND id != ?";
            $params[] = $except;
        }
        $query = $this->pdo->prepare($sql);
        $query->execute($params);
        return (int)$query->fetch(PDO::FETCH_NUM)[0] > 0;
    }

    /**
     * Fonction qui supprime un élément
     *
     * @param integer $id ID de l'élément
     * @return void
     */
    public function delete(int $id)
    {
        $query = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        $ok = $query->execute([$id]);
        if (!$ok)
            throw new \Exception("Impossible de supprimer l'enregristrement $id dans la table {$this->table}");
    }

    /**
     * Fonction qui crée un élément
     *
     * @param array $data tableau associatif clé => valeur du champ
     * @return void
     */
    public function create(array $data): int
    {
        $sqlFields = [];
        foreach ($data as $key => $value) {
            $sqlFields[] = "$key = :$key";
        }
        $query = $this->pdo->prepare("INSERT INTO {$this->table} SET " . implode(', ', $sqlFields));
        $ok = $query->execute($data);
        if (!$ok)
            throw new \Exception("Impossible de créer l'enregristrement dans la table {$this->table}");
        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Fonction qui update un élément
     *
     * @param array $data tableau associatif clé => valeur du champ
     * @param integer $id ID de l'élément
     * @return void
     */
    public function update(array $data, int $id): void
    {
        $sqlFields = [];
        foreach ($data as $key => $value) {
            $sqlFields[] = "$key = :$key";
        }
        $query = $this->pdo->prepare("UPDATE {$this->table} SET " . implode(', ', $sqlFields) . " WHERE id = :id");
        $ok = $query->execute(array_merge($data, ['id' => $id]));
        if (!$ok)
            throw new \Exception("Impossible de créer l'enregristrement dans la table {$this->table}");
    }

    /**
     * Fonction qui exécute la requête envoyée et fetchAll
     *
     * @param string $sql Requête SQL
     * @return array Tableau des résultats
     */
    public function queryAndFetchAll(string $sql): array
    {
        return $this->pdo->query($sql, PDO::FETCH_CLASS, $this->class)->fetchAll();
    }
}
