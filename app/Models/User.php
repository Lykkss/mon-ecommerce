<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public static function findById(int $id): ?array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, fullname)
            VALUES (:u, :e, :p, :f)
        ");
        $stmt->execute([
            'u' => $data['username'],
            'e' => $data['email'],
            // Hash du mot de passe reçu
            'p' => password_hash($data['password'], PASSWORD_BCRYPT),
            'f' => $data['fullname'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }
}
