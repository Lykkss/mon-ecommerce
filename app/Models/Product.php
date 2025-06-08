<?php
namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Product
{
    public static function all(): array
    {
        $db = Database::getInstance();
        return $db->query("
            SELECT
              p.id,
              p.name,
              p.description,
              p.price,
              p.image,
              p.category_id,
              cat.name AS category_name,
              COALESCE(s.quantity, 0) AS stock
            FROM products p
            LEFT JOIN stock    s   ON s.article_id   = p.id
            LEFT JOIN categories cat ON cat.id        = p.category_id
            ORDER BY p.created_at DESC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()->prepare("
            SELECT
              p.id,
              p.name,
              p.description,
              p.price,
              p.image,
              p.category_id,
              cat.name AS category_name,
              p.author_id,
              p.created_at
            FROM products p
            LEFT JOIN categories cat ON cat.id = p.category_id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO products
              (name, description, price, image, author_id, category_id, created_at)
            VALUES
              (:n, :d, :p, :i, :a, :c, NOW())
        ");
        $stmt->execute([
            'n' => $data['name'],
            'd' => $data['description'],
            'p' => $data['price'],
            'i' => $data['image'],
            'a' => $data['author_id'],
            'c' => $data['category_id'],
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db     = Database::getInstance();
        $fields = [];
        $params = ['id' => $id];

        if (isset($data['name'])) {
            $fields[]       = 'name = :name';
            $params['name'] = $data['name'];
        }
        if (isset($data['description'])) {
            $fields[]            = 'description = :description';
            $params['description']= $data['description'];
        }
        if (isset($data['price'])) {
            $fields[]       = 'price = :price';
            $params['price']= $data['price'];
        }
        if (isset($data['image'])) {
            $fields[]       = 'image = :image';
            $params['image']= $data['image'];
        }
        if (array_key_exists('category_id', $data)) {
            $fields[]            = 'category_id = :category';
            $params['category']  = $data['category_id'];
        }

        if (empty($fields)) {
            return;
        }

        $sql  = 'UPDATE products SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    }

    public static function delete(int $id): void
    {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $db->prepare("DELETE FROM invoice_items WHERE product_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM stock         WHERE article_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM comments      WHERE product_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM favorites     WHERE product_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM products      WHERE id = ?")       ->execute([$id]);
            $db->commit();
        } catch (PDOException $e) {
            $db->rollBack();
            throw $e;
        }
    }
}
