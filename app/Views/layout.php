<?php
use App\Models\User;

// 1) Récupération de l’utilisateur courant
$currentUser = null;
if (!empty($_SESSION['user_id'])) {
    $currentUser = User::findById((int) $_SESSION['user_id']);
}

/**
 * Vérifie que l’avatar existe et renvoie un chemin commençant par '/'
 *
 * @param string $relativePath  'assets/avatars/nomfichier.ext'
 * @return string|null          '/assets/avatars/nomfichier.ext' ou null
 */
function avatarUrl(string $relativePath): ?string {
    $fullPath = __DIR__ . '/../../public/' . ltrim($relativePath, '/');
    return file_exists($fullPath)
        ? '/' . ltrim($relativePath, '/')
        : null;
}

// 2) Détermine la source de l’avatar à afficher
$avatarSrc = null;
if ($currentUser && !empty($currentUser['avatar'])) {
    // Si l’utilisateur a uploadé un avatar valide
    $avatarSrc = avatarUrl($currentUser['avatar']);
}

// 3) Fallback vers default.png (facultatif : créer public/assets/avatars/default.png)
// Si vous ne voulez PAS de placeholder, supprimez ce bloc
if (!$avatarSrc && $currentUser) {
    $avatarSrc = '/assets/avatars/default.png';
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
<body class="bg-gray-100 text-gray-800">

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
        <a href="/compte" class="hover:underline">Mon compte</a>
        <a href="/panier" class="hover:underline">
          Panier (<?= array_sum($_SESSION['cart'] ?? []) ?>)
        </a>
        <a href="/logout" class="hover:underline">Déconnexion</a>
      <?php else: ?>
        <a href="/login" class="hover:underline">Connexion</a>
        <a href="/register" class="hover:underline">Inscription</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="container mx-auto p-4">
    <?php
      if (!empty($login)) {
        include __DIR__ . '/login.php';
      } elseif (!empty($register)) {
        include __DIR__ . '/register.php';
      } elseif (!empty($product)) {
        include __DIR__ . '/show.php';
      } elseif (!empty($checkout)) {
        include __DIR__ . '/checkout.php';
      } elseif (!empty($orderSuccess)) {
        include __DIR__ . '/order_success.php';
      } elseif (isset($items)) {
        include __DIR__ . '/cart.php';
      } elseif (!empty($account)) {
        include __DIR__ . '/account.php';
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
