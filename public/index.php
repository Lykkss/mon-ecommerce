<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\{
    HomeController,
    CartController,
    CheckoutController,
    AuthController,
    AccountController,
    SellController,
    InvoiceController,
    StockController as PublicStockController
};
use App\Controllers\Admin\{
    DashboardController,
    ProductController,
    UserController,
    StockController as AdminStockController
};

session_start();

$router = new Router();

// --- Authentification ---
$router->get ('/register',        [AuthController::class, 'registerForm']);
$router->post('/register',        [AuthController::class, 'registerSubmit']);
$router->get ('/login',           [AuthController::class, 'loginForm']);
$router->post('/login',           [AuthController::class, 'loginSubmit']);
$router->get ('/logout',          [AuthController::class, 'logout']);

// --- Catalogue public ---
$router->get ('/',                        [HomeController::class, 'index']);
$router->get ('/produit/(?P<id>\d+)',     [HomeController::class, 'show']);

// --- Panier (protégé) ---
$router->post('/panier/ajouter',          [CartController::class, 'add']);
$router->get ('/panier',                  [CartController::class, 'index']);
$router->post('/panier/supprimer',        [CartController::class, 'remove']);

// --- Checkout / commande (protégé) ---
$router->get ('/commande',                [CheckoutController::class, 'form']);
$router->post('/commande/valider',        [CheckoutController::class, 'submit']);

// --- Mon compte & factures (protégé) ---
$router->get ('/compte',                           [AccountController::class, 'index']);
$router->post('/compte/mettre-a-jour',             [AccountController::class, 'update']);
$router->get ('/compte/factures',                  [InvoiceController::class, 'index']);
$router->get ('/compte/facture/(?P<id>\d+)',       [InvoiceController::class, 'show']);
$router->get ('/compte/facture/(?P<id>\d+)/pdf',   [InvoiceController::class, 'pdf']);

// --- Gestion des articles (sell) ---
$router->get ('/sell',                  [SellController::class, 'createForm']);
$router->post('/sell',                  [SellController::class, 'createSubmit']);
$router->get ('/edit/(?P<id>\d+)',      [SellController::class, 'editForm']);
$router->post('/edit/(?P<id>\d+)',      [SellController::class, 'editSubmit']);
$router->post('/delete/(?P<id>\d+)',    [SellController::class, 'delete']);

// --- Ajustement du stock (public, protégé) ---
$router->post(
    '/produit/(?P<id>\d+)/stock/ajouter',
    [PublicStockController::class, 'increase']
);

// --- PANEL ADMIN (protégé automatiquement par Router) ---
// Dashboard
$router->get('/admin',                  [DashboardController::class, 'index']);

// Consultation du stock en back-office
$router->get('/admin/stock',            [AdminStockController::class, 'index']);
$router->post('/admin/stock/update', [AdminStockController::class, 'updateSubmit']);

// CRUD produits
$router->get ('/admin/products',               [ProductController::class, 'index']);
$router->get ('/admin/products/create',        [ProductController::class, 'createForm']);
$router->post('/admin/products/create',        [ProductController::class, 'createSubmit']);
$router->get ('/admin/products/edit/(?P<id>\d+)', [ProductController::class, 'editForm']);
$router->post('/admin/products/edit/(?P<id>\d+)', [ProductController::class, 'editSubmit']);
$router->post('/admin/products/delete/(?P<id>\d+)', [ProductController::class, 'delete']);

// CRUD utilisateurs
$router->get ('/admin/users',               [UserController::class, 'index']);
$router->get ('/admin/users/edit/(?P<id>\d+)', [UserController::class, 'editForm']);
$router->post('/admin/users/edit/(?P<id>\d+)', [UserController::class, 'editSubmit']);
$router->post('/admin/users/delete/(?P<id>\d+)', [UserController::class, 'delete']);
$router->get('/admin/users/create', [UserController::class, 'createForm']);
$router->post('/admin/users/create', [UserController::class, 'createSubmit']);


// --- Dispatch final (affiche la page ou 404/403) ---
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
