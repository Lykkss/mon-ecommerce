<?php
use App\Models\User;

// Récupération de l’utilisateur courant
$currentUser = null;
if (!empty($_SESSION['user_id'])) {
    $currentUser = User::findById((int) $_SESSION['user_id']);
}

// Fonction d’avatar
function avatarUrl(string $relativePath): ?string {
    $path = ltrim($relativePath, '/');
    $full = __DIR__ . '/../../public/' . $path;
    return file_exists($full) ? '/' . $path : null;
}

$avatarSrc = null;
if ($currentUser && !empty($currentUser['avatar'])) {
    $avatarSrc = avatarUrl($currentUser['avatar']);
}
$fallback = '/assets/avatars/default.png';
if (!$avatarSrc && $currentUser && file_exists(__DIR__ . '/../../public' . $fallback)) {
    $avatarSrc = $fallback;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>PokéCommerce</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

  <header class="bg-blue-800 text-white p-4 flex justify-between items-center">
    <div class="flex items-center space-x-2">
      <a href="/" class="text-xl font-bold hover:underline">PokéCommerce</a>
      <?php if ($avatarSrc): ?>
        <img src="<?= htmlspecialchars($avatarSrc, ENT_QUOTES) ?>"
             alt="Avatar"
             class="h-8 w-8 rounded-full object-cover">
      <?php endif; ?>
    </div>
    <nav class="space-x-4">
      <?php if ($currentUser): ?>
        <span>Bonjour, <?= htmlspecialchars($currentUser['fullname'] ?: $currentUser['username'], ENT_QUOTES) ?></span>
        <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
          <a href="/admin" class="hover:underline font-semibold">Dashboard Admin</a>
        <?php endif; ?>
        <a href="/compte" class="hover:underline">Mon compte</a>
        <a href="/favoris" class="hover:underline">Mes favoris</a>
        <a href="/panier" class="hover:underline">Panier (<?= array_sum($_SESSION['cart'] ?? []) ?>)</a>
        <a href="/logout" class="hover:underline">Déconnexion</a>
      <?php else: ?>
        <a href="/login" class="hover:underline">Connexion</a>
        <a href="/register" class="hover:underline">Inscription</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="container mx-auto p-4 flex-grow">
    <?php
      // 1) Connexion
      if (!empty($login)):
          include __DIR__ . '/login.php';
          return;

      // 2) Checkout
      elseif (!empty($checkout)):
          include __DIR__ . '/checkout.php';
          return;

      // 3) Succès de commande
      elseif (!empty($orderSuccess)):
          include __DIR__ . '/order_success.php';
          return;

      // 4) Panier
      elseif (isset($items) && !isset($invoice)):
          include __DIR__ . '/cart.php';
          return;

      // 5) Dashboard Admin
      elseif (!empty($adminDashboard)):
          include __DIR__ . '/admin/dashboard.php';
          return;

      // 6) Admin – produits
      elseif (!empty($adminProducts)):
          include __DIR__ . '/admin/products.php';
          return;
      elseif (!empty($adminProductsCreate) || !empty($adminProductsEdit)):
          include __DIR__ . '/admin/product_form.php';
          return;

      // 7) Admin – utilisateurs
      elseif (!empty($adminUsers)):
          include __DIR__ . '/admin/users.php';
          return;
      elseif (!empty($adminUsersCreate) || !empty($adminUsersEdit)):
          include __DIR__ . '/admin/user_form.php';
          return;

      // 8) Admin – stock
      elseif (!empty($adminStock)):
          include __DIR__ . '/admin/stock.php';
          return;

      // 9) Admin – commentaires
      elseif (!empty($adminComments)):
          include __DIR__ . '/admin/comments.php';
          return;

      // 10) Admin – favoris
      elseif (!empty($adminFavorites)):
          include __DIR__ . '/admin/favorites.php';
          return;

      // 11) Admin – catégories
      elseif (!empty($adminCategories)):
          include __DIR__ . '/admin/categories.php';
          return;

      // 12) Inscription
      elseif (!empty($register)):
          include __DIR__ . '/register.php';
          return;

      // 13) Détail produit
      elseif (!empty($productView)):
          include __DIR__ . '/show.php';
          return;

      // 14) Mon compte
      elseif (!empty($account)):
          include __DIR__ . '/account.php';
          return;

      // 15) Liste factures
      elseif (isset($invoices)):
          include __DIR__ . '/invoice_list.php';
          return;

      // 16) Détail facture
      elseif (isset($invoice) && isset($items)):
          include __DIR__ . '/invoice_detail.php';
          return;

      // 17) Vendre / éditer un produit
      elseif (!empty($sell) || !empty($edit)):
          include __DIR__ . '/sell.php';
          return;

      // 18) Mes favoris
      elseif (!empty($favorites)):
          include __DIR__ . '/favorites.php';
          return;

      // 19) Terms
      elseif (!empty($terms)):
          include __DIR__ . '/terms.php';
          return;

      // 20) Page d’accueil
      else:
          include __DIR__ . '/home.php';
          return;
      endif;
    ?>
  </main>

  <footer class="bg-gray-800 text-white p-4 text-center">
    &copy; <?= date('Y') ?> Pokémon Commerce
  </footer>
</body>
</html>
