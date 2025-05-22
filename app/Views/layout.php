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
    <div class="space-x-4">
      <?php if (!empty($_SESSION['user_id'])): ?>
        <a href="/logout" class="hover:underline">Déconnexion</a>
        <a href="/panier" class="hover:underline">
          Panier (<?= array_sum($_SESSION['cart'] ?? []) ?>)
        </a>
      <?php else: ?>
        <a href="/login" class="hover:underline">Connexion</a>
        <a href="/register" class="hover:underline">Inscription</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="container mx-auto p-4">
    <?php
      if (isset($login) && $login === true) {
        include __DIR__ . '/login.php';

      } elseif (isset($register) && $register === true) {
        include __DIR__ . '/register.php';

      } elseif (isset($product)) {
        include __DIR__ . '/show.php';

      } elseif (isset($checkout) && $checkout === true) {
        include __DIR__ . '/checkout.php';

      } elseif (isset($orderSuccess) && $orderSuccess === true) {
        include __DIR__ . '/order_success.php';

      } elseif (isset($items)) {
        include __DIR__ . '/cart.php';

      } else {
        include __DIR__ . '/home.php';
      }
    ?>
  </main>

  <footer class="bg-gray-800 text-white p-4 text-center">
    &copy; 2025 Pokémon Commerce
  </footer>
</body>
</html>
