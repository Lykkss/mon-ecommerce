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
      // 5) Formulaire de connexion en pleine page
      if (!empty($login)):
          include __DIR__ . '/login.php';
          return;  // on arrête ici

      // 1) Panier
      elseif (isset($items) && !isset($invoice)): 
          include __DIR__ . '/cart.php';

      // 2) Admin Dashboard
      elseif (!empty($adminDashboard)): 
          include __DIR__ . '/admin/dashboard.php';

      // 3) Admin produits
      elseif (!empty($adminProducts)):
          include __DIR__ . '/admin/products.php';
      elseif (!empty($adminProductsCreate) || !empty($adminProductsEdit)):
          include __DIR__ . '/admin/product_form.php';

      // 4) Admin users
      elseif (!empty($adminUsers)):
          include __DIR__ . '/admin/users.php';
      elseif (!empty($adminUsersCreate) || !empty($adminUsersEdit)):
          include __DIR__ . '/admin/user_form.php';

      // 5) Admin Stock
      elseif (!empty($adminStock)):
          include __DIR__ . '/admin/stock.php';

      // 6) Public views: Inscription
      elseif (!empty($register)):
          include __DIR__ . '/register.php';
      // 7) Public: Détail produit
      elseif (!empty($product)):
          include __DIR__ . '/show.php';
      // 8) Public: Checkout
      elseif (!empty($checkout)):
          include __DIR__ . '/checkout.php';
      // 9) Public: Page Merci
      elseif (!empty($orderSuccess)):
          include __DIR__ . '/order_success.php';
      // 10) Public: Sell / Édition
      elseif (!empty($sell)):
          include __DIR__ . '/sell.php';
      elseif (!empty($edit)):
          include __DIR__ . '/edit.php';
      // 11) Espace Mon compte
      elseif (!empty($account)):
          include __DIR__ . '/account.php';

      // 12) Liste des factures
      elseif (isset($invoices)):
          include __DIR__ . '/invoice_list.php';

      // 13) Détail d’une facture
      elseif (isset($invoice) && isset($items)):
          include __DIR__ . '/invoice_detail.php';

      // 14) Page d’accueil par défaut
      else:
          include __DIR__ . '/home.php';
      endif;
    ?>
  </main>

  <footer class="bg-gray-800 text-white p-4 text-center">
    &copy; <?= date('Y') ?> Pokémon Commerce
  </footer>
</body>
</html>
