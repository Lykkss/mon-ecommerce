<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>PokéCommerce</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

  <header class="bg-blue-800 text-white p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">
      <a href="/" class="hover:underline">PokéCommerce</a>
    </h1>
    <a href="/panier" class="hover:underline">
      Panier (<?= array_sum($_SESSION['cart'] ?? []) ?>)
    </a>
  </header>

  <main class="container mx-auto p-4">
    <?php
      if (isset($product)) {
        // page de détail
        include __DIR__ . '/show.php';

      } elseif (isset($items)) {
        // page panier
        include __DIR__ . '/cart.php';

      } else {
        // page d’accueil (grille)
        include __DIR__ . '/home.php';
      }
    ?>
  </main>

  <footer class="bg-gray-800 text-white p-4 text-center">
    &copy; 2025 Pokémon Commerce
  </footer>
</body>
</html>
