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
     * Récupère tous les utilisateurs (pour lister en back‐office)
     */
    public static function all(): array
    {
        $stmt = Database::getInstance()
            ->query("SELECT * FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouvel utilisateur
     *
     * Attente : $data['password'] est déjà haché en amont.
     */
    public static function create(array $data): int
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            "INSERT INTO users 
               (username, email, password, fullname, role, avatar)
             VALUES 
               (:u, :e, :p, :f, :r, :a)"
        );
        $stmt->execute([
            'u' => $data['username'],  
            'e' => $data['email'],     
            'p' => $data['password'],  
            'f' => $data['fullname'] ?? null,
            'r' => $data['role'] ?? 'user',
            'a' => $data['avatar'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    /**
     * Met à jour un utilisateur
     *
     * Si $data['password'] est renseigné, on s’attend à ce que ce soit un mot de passe en clair ;
     * on l’hache à ce stade.
     */
    public static function update(int $id, array $data): void
    {
        $sets   = [];
        $params = [];

        foreach (['username', 'email', 'fullname', 'role'] as $field) {
            if (array_key_exists($field, $data)) {
                $sets[]         = "`$field` = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (isset($data['password'])) {
            $sets[]               = "`password` = :password";
            $params['password']   = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (array_key_exists('avatar', $data)) {
            $sets[]              = "`avatar` = :avatar";
            $params['avatar']    = $data['avatar'];
        }

        $params['id'] = $id;
        $sql = "UPDATE users 
                   SET " . implode(', ', $sets) . " 
                 WHERE id = :id";
        Database::getInstance()
            ->prepare($sql)
            ->execute($params);
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
