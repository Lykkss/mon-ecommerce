<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Product
{
    public static function all(): array
    {
        $db = Database::getInstance();
        $sql = "
          SELECT
            p.id,
            p.name,
            p.description,
            p.price,
            p.image,
            COALESCE(s.quantity, 0) AS stock,
            p.author_id,
            p.created_at
          FROM products p
          LEFT JOIN stock s ON s.article_id = p.id
          ORDER BY p.created_at DESC
        ";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT
                           p.id,
                           p.name,
                           p.description,
                           p.price,
                           p.image,
                           p.author_id,
                           p.created_at
                       FROM products p
                       WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO products (name, description, price, image, author_id)
                              VALUES (:n, :d, :p, :i, :a)");
        $stmt->execute([
            'n' => $data['name'],    
            'd' => $data['description'],
            'p' => $data['price'],   
            'i' => $data['image'],   
            'a' => $data['author_id'],
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE products
                               SET name        = :n,
                                   description = :d,
                                   price       = :p,
                                   image       = :i,
                                   author_id   = :a
                             WHERE id = :id");
        $stmt->execute([
            'n'  => $data['name'],  
            'd'  => $data['description'], 
            'p'  => $data['price'], 
            'i'  => $data['image'], 
            'a'  => $data['author_id'],
            'id' => $id,
        ]);
    }

    public static function delete(int $id): void
    {
        Database::getInstance()
            ->prepare("DELETE FROM products WHERE id = ?")
            ->execute([$id]);
    }
}