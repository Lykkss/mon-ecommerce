<?php // app/Views/show.php ?>
<div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-md overflow-hidden md:flex">
  <?php if (!empty($product['image'])): ?>
    <img src="/<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>"
         alt="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
         class="w-full md:w-1/2 h-64 object-cover">
  <?php endif; ?>

  <div class="p-6 flex flex-col justify-between">
    <div>
      <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($product['name'], ENT_QUOTES) ?></h1>
      <p class="text-gray-700 mb-6"><?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES)) ?></p>
    </div>
    <div class="flex items-center justify-between">
      <span class="text-2xl font-bold"><?= number_format($product['price'], 2, ',', ' ') ?> €</span>
      <form action="/panier/ajouter" method="post" class="flex items-center space-x-2">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
        <input type="number" name="quantity" value="1" min="1" max="<?= $stock ?>"
               class="w-16 border rounded text-center">
        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-2xl">
          Ajouter au panier
        </button>
        <button type="button" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl"
                onclick="window.location.href='/commande'">
          Commander 
      </form>
    </div>
  </div>
</div>
