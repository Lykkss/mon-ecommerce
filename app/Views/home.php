<?php // app/Views/home.php

/** @var array[] $products */
$publicDir = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
?>

<h2 class="text-2xl font-bold mb-4">Catalogue</h2>

<?php if (empty($products)): ?>
  <p>Aucun produit disponible.</p>
<?php else: ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($products as $p): ?>
      <?php
        // Chemin relatif et absolu de l'image
        $rel = ltrim($p['image'] ?? '', '/');
        $full = $publicDir . '/' . $rel;
      ?>
      <div class="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col hover:shadow-lg transition">
        <a href="/produit/<?= (int)$p['id'] ?>" class="block flex-1">
          <?php if ($rel && file_exists($full)): ?>
            <img src="/<?= htmlspecialchars($rel, ENT_QUOTES) ?>" 
                 alt="<?= htmlspecialchars($p['name'], ENT_QUOTES) ?>" 
                 class="h-48 w-full object-cover">
          <?php else: ?>
            <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
              <span class="text-gray-400 italic">Pas d’image</span>
            </div>
          <?php endif; ?>
          <div class="p-4 flex flex-col">
            <h2 class="text-lg font-semibold mb-2"><?= htmlspecialchars($p['name'], ENT_QUOTES) ?></h2>
            <p class="text-gray-600 flex-1"><?= nl2br(htmlspecialchars($p['description'], ENT_QUOTES)) ?></p>
          </div>
        </a>

        <div class="p-4 mt-auto flex items-center justify-between border-t bg-white">
          <span class="text-xl font-bold"><?= number_format((float)$p['price'], 2, ',', ' ') ?> €</span>
          <form action="/panier/ajouter" method="post" class="flex items-center space-x-2">
            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
            <input type="number" name="quantity" value="1" min="1" 
                   max="<?= isset($p['stock']) ? (int)$p['stock'] : '' ?>" 
                   class="w-16 border rounded text-center">
            <button type="submit" 
                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-xl">
              Ajouter
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="mt-6">
  <a href="/produits" class="text-blue-600 hover:underline">Voir tous les produits</a>
</div>

<script>
  // Empêcher l’ajout au panier si l’utilisateur n’est pas connecté
  const isLogged = <?= json_encode(isset($_SESSION['user_id'])) ?>;
  document.querySelectorAll('form[action="/panier/ajouter"] button').forEach(btn => {
    btn.addEventListener('click', function(event) {
      if (!isLogged) {
        event.preventDefault();
        alert('Vous devez être connecté pour ajouter un produit au panier.');
      }
    });
  });
</script>
