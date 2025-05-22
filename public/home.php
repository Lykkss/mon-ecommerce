<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
  <?php foreach($products as $p): ?>
    <div class="bg-white rounded-2xl shadow-md overflow-hidden flex flex-col">
      <?php if(!empty($p['image'])): ?>
        <img src="/assets/<?= htmlspecialchars($p['image']) ?>"
             alt="<?= htmlspecialchars($p['name']) ?>"
             class="h-48 w-full object-cover">
      <?php else: ?>
        <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
          <span class="text-gray-400">Pas d’image</span>
        </div>
      <?php endif; ?>

      <div class="p-4 flex-1 flex flex-col">
        <h2 class="text-lg font-semibold mb-2">
          <a href="/produit/<?= $p['id'] ?>" class="hover:underline">
            <?= htmlspecialchars($p['name']) ?>
          </a>
        </h2>
        <p class="text-gray-600 flex-1"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
        <div class="mt-4 flex items-center justify-between">
          <span class="text-xl font-bold"><?= number_format($p['price'], 2, ',', ' ') ?> €</span>
          <form action="/panier/ajouter" method="post" class="flex items-center space-x-2">
            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
            <input type="number" name="quantity" value="1" min="1"
                   class="w-16 border rounded text-center">
            <button type="submit"
                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-xl">
              Ajouter
            </button>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
