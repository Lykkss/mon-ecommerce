<?php
namespace App\Controllers\Admin;

use App\Core\Database;
use App\Models\Category;

class DashboardController
{
    public function index(): void
    {
        // 1) Protection : seul un admin peut accéder
        if (empty($_SESSION['user_id'])
            || ($_SESSION['user_role'] ?? '') !== 'admin') {
            header('HTTP/1.0 403 Forbidden');
            exit('Accès refusé');
        }

        $db = Database::getInstance();

        // 2) KPIs
        $totalUsers      = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $totalProducts   = (int) $db->query('SELECT COUNT(*) FROM products')->fetchColumn();
        $totalSales      = (float) $db->query('SELECT COALESCE(SUM(total_amount),0) FROM invoices')->fetchColumn();
        $totalCategories = Category::countAll();  

        // 3) Dernières commandes
        $stmt = $db->prepare(
            'SELECT id, user_id, total_amount, created_at
             FROM invoices
             ORDER BY created_at DESC
             LIMIT 5'
        );
        $stmt->execute();
        $recentOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 4) Flag pour la vue
        $adminDashboard = true;

        // 5) Appel du layout
        require __DIR__ . '/../../Views/layout.php';
    }
}
