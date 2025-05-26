<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    /**
     * Récupère un utilisateur par email
     */
    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Récupère un utilisateur par son ID
     */
    public static function findById(int $id): ?array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Crée un nouvel utilisateur
     */
    public static function create(array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO users (username, email, password, fullname, role)"
          . " VALUES (:u, :e, :p, :f, :r)"
        );
        $stmt->execute([
            'u' => $data['username'],  
            'e' => $data['email'],     
            'p' => password_hash($data['password'], PASSWORD_BCRYPT),
            'f' => $data['fullname'] ?? null,
            'r' => $data['role'] ?? 'user',
        ]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Récupère tous les utilisateurs
     */
    public static function all(): array
    {
        $stmt = Database::getInstance()
            ->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour un utilisateur
     */
    public static function update(int $id, array $data): void
    {
        $sets = [];
        $params = [];
        foreach (['username','email','fullname','role'] as $field) {
            if (array_key_exists($field, $data)) {
                $sets[] = "`$field` = :$field";
                $params[$field] = $data[$field];
            }
        }
        if (isset($data['password'])) {
            $sets[] = "`password` = :password";
            $params['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        $params['id'] = $id;
        $sql = "UPDATE users SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = Database::getInstance()->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * Supprime un utilisateur
     */
    public static function delete(int $id): void
    {
        Database::getInstance()
            ->prepare("DELETE FROM users WHERE id = ?")
            ->execute([$id]);
    }

    /**
     * Compte tous les utilisateurs (pour le dashboard)
     */
    public static function countAll(): int
    {
        $stmt = Database::getInstance()
            ->query("SELECT COUNT(*) as cnt FROM users");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($row['cnt'] ?? 0);
    }
}
