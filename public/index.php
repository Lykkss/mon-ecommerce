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
    SellController
};

session_start();

$router = new Router();

// --- Authentification ---
$router->get ('/register',       [AuthController::class, 'registerForm']);
$router->post('/register',       [AuthController::class, 'registerSubmit']);
$router->get ('/login',          [AuthController::class, 'loginForm']);
$router->post('/login',          [AuthController::class, 'loginSubmit']);
$router->get ('/logout',         [AuthController::class, 'logout']);

// --- Catalogue produits (public) ---
$router->get ('/',               [HomeController::class, 'index']);
$router->get ('/produit/(?P<id>\d+)', [HomeController::class, 'show']);

// --- Panier (protégé) ---
$router->post('/panier/ajouter', [CartController::class, 'add']);
$router->get ('/panier',         [CartController::class, 'index']);
$router->post('/panier/supprimer',[CartController::class, 'remove']);

// --- Checkout / commande (protégé) ---
$router->get ('/commande',       [CheckoutController::class, 'form']);
$router->post('/commande/valider',[CheckoutController::class, 'submit']);

// --- Mon compte (protégé) ---
$router->get ('/compte',               [AccountController::class, 'index']);
$router->post('/compte/mettre-a-jour', [AccountController::class, 'update']);

// --- Gestion des articles ---
$router->get ('/sell',               [SellController::class, 'createForm']);
$router->post('/sell',               [SellController::class, 'createSubmit']);
$router->get ('/edit/(?P<id>\d+)',   [SellController::class, 'editForm']);
$router->post('/edit/(?P<id>\d+)',   [SellController::class, 'editSubmit']);
$router->post('/delete/(?P<id>\d+)', [SellController::class, 'delete']);



// Dispatch final
echo $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);