<?php

namespace jidaikobo\kontiki\Models;

use PDO;

class User extends BaseModel
{
    protected PDO $pdo;

    protected string $table = 'users';

    protected static array $fieldDefinitions = [
        'username' => [
    //            'rules' => ['required', ['lengthMin', 3]],
            'default' => '',
            'filter' => FILTER_SANITIZE_STRING,
        ],
        'password' => [
        //            'rules' => ['required', ['lengthMin', 3]],
            'default' => '',
            'filter' => FILTER_UNSAFE_RAW,
        ],
    ];

    /**
     * Get the user by their username.
     *
     * @param  string $username The username to search for.
     * @return array|null user information, or null if not.
     */
    public function getByUsername(string $username): ?array
    {
        $query = "SELECT password FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['username' => $username]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?? null;
    }
}
