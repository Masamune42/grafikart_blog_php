<?php

namespace App\Table;

use App\Model\User;
use App\Table\Table;
use App\Table\Exception\NotFoundException;
use PDO;

final class UserTable extends Table
{
    protected $table = "user";
    protected $class = User::class;

    /**
     * Recherche un utilisateur par son pseudo
     *
     * @param string $username pseudo utilisateur
     * @return User L'utilisateur trouvé
     */
    public function findByUsername(string $username)
    {
        $query = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE username = :username');
        $query->execute(['username' => $username]);
        $query->setFetchMode(PDO::FETCH_CLASS, $this->class);
        /** @var User|false */
        $result = $query->fetch();
        if ($result === false) {
            throw new NotFoundException($this->table, $username);
        }
        return $result;
    }
}
