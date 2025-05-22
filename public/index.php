<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pokemon commerce</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
  <!-- votre markup ici -->
  <?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Core\Router;

// Démarrage de la session
session_start();

// Instanciation du routeur
$router = new Router();

// Définition des routes
$router->get('/', 'HomeController@index');
$router->get('/produit/(?P<id>\d+)', 'HomeController@show');
// … autres routes (panier, compte, etc.)

// Lancement
$router->post('/panier/ajouter', 'CartController@add');
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
?>
  <header class="bg-blue-600 text-white p-4">
    <h1 class="text-2xl">Pokémon Commerce</h1>
  </header>
  <main class="container mx-auto p-4">
    <h2 class="text-xl mb-4">Bienvenue sur notre site de vente de Pokémon !</h2>
    <p>Parcourez nos produits et passez commande en toute sécurité.</p>
    <!-- Ajoutez ici le contenu dynamique généré par le routeur -->
  </main>
  <footer class="bg-gray-800 text-white p-4 text-center">
    <p>&copy; 2025 Pokémon Commerce. Tous droits réservés.</p>
  </footer>
</body>
</html>
