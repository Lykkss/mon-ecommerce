<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\AuthController;
use App\Controllers\AccountController;

session_start();

$router = new Router();

// — Authentification
$router->get('/register',  [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'registerSubmit']);
$router->get('/login',     [AuthController::class, 'loginForm']);
$router->post('/login',    [AuthController::class, 'loginSubmit']);
$router->get('/logout',    [AuthController::class, 'logout']);

// — Catalogue produits (public)
$router->get('/',                         [HomeController::class, 'index']);
$router->get('/produit/(?P<id>\d+)',      [HomeController::class, 'show']);

// — Panier (protégé)
$router->post('/panier/ajouter',   [CartController::class, 'add']);
$router->get('/panier',            [CartController::class, 'index']);
$router->post('/panier/supprimer', [CartController::class, 'remove']);

// — Checkout / commande (protégé)
$router->get('/commande',          [CheckoutController::class, 'form']);
$router->post('/commande/valider', [CheckoutController::class, 'submit']);

// — Mon compte (protégé)
// GET page de compte
$router->get('/compte',               [AccountController::class, 'index']);
// GET fallback après POST pour ne pas 404
$router->get('/compte/mettre-a-jour', [AccountController::class, 'index']);
// POST de mise à jour du profil
$router->post('/compte/mettre-a-jour',[AccountController::class, 'update']);

// Dispatch final
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
