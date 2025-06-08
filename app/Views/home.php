<?php // app/Views/home.php

/** @var array[] $products */
/** @var array[] $categories */
$publicDir  = rtrim($_SERVER['DOCUMENT_ROOT'], '/');

// Valeurs de filtres préremplies
$q           = htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES);
$priceMin    = htmlspecialchars($_GET['price_min'] ?? '', ENT_QUOTES);
$priceMax    = htmlspecialchars($_GET['price_max'] ?? '', ENT_QUOTES);
$inStock     = !empty($_GET['in_stock']);
$catSel      = (int)($_GET['category_id'] ?? 0);
?>

<h2 class="text-2xl font-bold mb-4">Catalogue Pokémon</h2>

<form method="get" action="/" class="bg-white p-4 rounded shadow mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
  <!-- Prix min -->
  <div>
    <label class="block text-sm font-medium">Prix min (€)</label>
    <input type="number" step="0.01" name="price_min" value="<?= $priceMin ?>"
           class="w-full border rounded p-2">
  </div>

  <!-- Prix max -->
  <div>
    <label class="block text-sm font-medium">Prix max (€)</label>
    <input type="number" step="0.01" name="price_max" value="<?= $priceMax ?>"
           class="w-full border rounded p-2">
  </div>

  <!-- Catégorie -->
<!-- Catégorie -->
<div>
  <label class="block text-sm font-medium">Catégorie</label>
  <select name="category_id" class="w-full border rounded p-2">
    <option value="0">Toutes</option>
    <?php foreach($categories as $cat): ?>
      <option value="<?= (int)$cat['id'] ?>"
        <?= ((int)$cat['id'] === $catSel) ? 'selected' : '' ?>>
        <?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>

  <!-- En stock uniquement -->
  <div class="flex items-end">
    <label class="inline-flex items-center space-x-2 text-sm">
      <input type="checkbox" name="in_stock" value="1" <?= $inStock ? 'checked' : '' ?>>
      <span>En stock uniquement</span>
    </label>
  </div>

  <!-- Boutons -->
  <div class="col-span-full flex space-x-2 mt-2">
    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
      Filtrer
    </button>
    <a href="/" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
      Réinitialiser
    </a>
  </div>
</form>

<?php if (empty($products)): ?>
  <p>Aucun produit disponible selon ces critères.</p>
<?php else: ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($products as $p): ?>
      <?php
        $rel  = ltrim($p['image'] ?? '', '/');
        $full = "$publicDir/$rel";
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
          <span class="text-xl font-bold"><?= number_format((float)$p['price'],2,',',' ') ?> €</span>
          <form action="/panier/ajouter" method="post" class="flex items-center space-x-2">
            <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
            <input type="number" name="quantity" value="1" min="1"
                   max="<?= (int)$p['stock'] ?>"
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

<script>
  // Empêcher l’ajout au panier si l’utilisateur n’est pas connecté
  const isLogged = <?= json_encode(isset($_SESSION['user_id'])) ?>;
  document.querySelectorAll('form[action="/panier/ajouter"] button').forEach(btn => {
    btn.addEventListener('click', e => {
      if (!isLogged) {
        e.preventDefault();
        alert('Connectez-vous pour ajouter au panier.');
      }
    });
  });
</script>
