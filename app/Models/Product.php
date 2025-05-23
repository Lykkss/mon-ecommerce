<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Product
{
    /**
     * Récupère tous les produits
     *
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        $stmt = Database::getInstance()
            ->query("SELECT * FROM products");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un produit par son ID
     *
     * @param int $id
     * @return array<string, mixed>|null
     */
    public static function find(int $id): ?array
    {
        $stmt = Database::getInstance()
            ->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Crée un nouveau produit en base
     *
     * @param array<string, mixed> $data [
     *      'title'       => string,
     *      'description' => string,
     *      'price'       => float,
     *      'image'       => string|null,
     *      'author_id'   => int
     * ]
     * @return int ID du produit créé
     */
    public static function create(array $data): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "INSERT INTO products (title, description, price, image, author_id) " .
            "VALUES (:t, :d, :p, :i, :u)"
        );
        $stmt->execute([
            't' => $data['title'],
            'd' => $data['description'],
            'p' => $data['price'],
            'i' => $data['image'],
            'u' => $data['author_id'],
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Met à jour un produit existant
     *
     * @param int $id
     * @param array<string, mixed> $data [
     *      'title'       => string,
     *      'description' => string,
     *      'price'       => float,
     *      'image'       => string|null
     * ]
     * @return void
     */
    public static function update(int $id, array $data): void
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            "UPDATE products SET title = :t, description = :d, price = :p, image = :i " .
            "WHERE id = :id"
        );
        $stmt->execute([
            't'  => $data['title'],
            'd'  => $data['description'],
            'p'  => $data['price'],
            'i'  => $data['image'],
            'id' => $id,
        ]);
    }

    /**
     * Supprime un produit
     *
     * @param int $id
     */
    public static function delete(int $id): void
    {
        Database::getInstance()
            ->prepare("DELETE FROM products WHERE id = ?")
            ->execute([$id]);
    }
}
