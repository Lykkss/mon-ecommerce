<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\CartController;

session_start();

$router = new Router();

// Routes produit
$router->get('/', [HomeController::class, 'index']);
$router->get('/produit/(?P<id>\d+)', [HomeController::class, 'show']);

// Routes panier
$router->post('/panier/ajouter', [CartController::class, 'add']);
$router->get('/panier',         [CartController::class, 'index']);
$router->post('/panier/supprimer', [CartController::class, 'remove']);

$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
