<?php
/** @var array[] $products */
?>

<h2 class="text-2xl font-bold mb-4">Mes favoris</h2>

<?php if (empty($products)): ?>
  <p>Vous n’avez ajouté aucun produit aux favoris.</p>
<?php else: ?>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($products as $prod): ?>
      <div class="bg-white p-4 rounded shadow">
        <?php if (!empty($prod['image'])): ?>
          <img src="/<?= htmlspecialchars($prod['image'], ENT_QUOTES) ?>"
               alt="<?= htmlspecialchars($prod['name'], ENT_QUOTES) ?>"
               class="w-full h-32 object-cover rounded mb-2">
        <?php else: ?>
          <div class="w-full h-32 bg-gray-200 rounded mb-2 flex items-center justify-center text-gray-500">
            Pas d’image
          </div>
        <?php endif; ?>

        <h3 class="font-semibold text-lg mb-1">
          <a href="/produit/<?= $prod['id'] ?>" class="hover:underline">
            <?= htmlspecialchars($prod['name'], ENT_QUOTES) ?>
          </a>
        </h3>
        <p class="text-sm text-gray-600 mb-2">
          <?= number_format($prod['price'], 2, ',', ' ') ?> €
        </p>
        <p class="text-sm mb-2">
          Stock : 
          <?php if ($prod['stock'] > 0): ?>
            <span class="text-green-600"><?= $prod['stock'] ?></span>
          <?php else: ?>
            <span class="text-red-600">Rupture</span>
          <?php endif; ?>
        </p>

        <!-- Bouton retirer des favoris -->
        <form action="/produit/<?= $prod['id'] ?>/favorite" method="post">
          <button type="submit"
                  class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded text-sm">
            Retirer des favoris
          </button>
        </form>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
