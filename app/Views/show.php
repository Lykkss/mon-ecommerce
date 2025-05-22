<div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-md overflow-hidden md:flex">
  <?php if($product['image']): ?>
    <img src="/assets/<?= htmlspecialchars($product['image']) ?>"
         alt="<?= htmlspecialchars($product['name']) ?>"
         class="w-full md:w-1/2 h-64 object-cover">
  <?php endif; ?>

  <div class="p-6 flex flex-col justify-between">
    <div>
      <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($product['name']) ?></h1>
      <p class="text-gray-700 mb-6"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
    </div>
    <div class="flex items-center justify-between">
      <span class="text-2xl font-bold"><?= number_format($product['price'], 2, ',', ' ') ?> €</span>
      <form action="/panier/ajouter" method="post" class="flex items-center space-x-2">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <input type="number" name="quantity" value="1" min="1" class="w-16 border rounded text-center">
        <button type="submit"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-2xl">
          Ajouter au panier
        </button>
      </form>
    </div>
  </div>
</div>
