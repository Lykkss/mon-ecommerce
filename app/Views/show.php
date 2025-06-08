<?php // app/Views/show.php

/** @var array $product */
/** @var int $stock */

// Vérifier si l'utilisateur connecté est l'auteur du produit
$isAuthor = isset($_SESSION['user_id'], $product['author_id'])
    && $_SESSION['user_id'] === $product['author_id'];

?>

<div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-md overflow-hidden md:flex">
  <?php if (!empty($product['image'])): ?>
    <img src="/<?= htmlspecialchars($product['image'], ENT_QUOTES) ?>"
         alt="<?= htmlspecialchars($product['name'], ENT_QUOTES) ?>"
         class="w-full md:w-1/2 h-64 object-cover">
  <?php endif; ?>

  <div class="p-6 flex flex-col justify-between space-y-6">
    <div>
      <h1 class="text-2xl font-bold mb-4"><?= htmlspecialchars($product['name'], ENT_QUOTES) ?></h1>
      <p class="text-gray-700 mb-4"><?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES)) ?></p>
      <p class="mb-4">
        <strong>En stock :</strong>
        <?= $stock > 0
            ? htmlspecialchars($stock, ENT_QUOTES) . ' exemplaire' . ($stock > 1 ? 's' : '')
            : '<span class="text-red-600">Rupture de stock</span>' ?>
      </p>

      <?php if ($isAuthor): ?>
        <!-- Formulaire d'augmentation du stock, visible uniquement pour l'auteur -->
        <form action="/produit/<?= (int)$product['id'] ?>/stock/ajouter"
              method="post"
              class="flex items-center space-x-2 mb-4">
          <label class="block">
            Ajouter au stock :
            <input type="number"
                   name="quantity"
                   min="1"
                   value="1"
                   class="w-16 border rounded text-center">
          </label>
          <button type="submit"
                  class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded">
            Mettre à jour le stock
          </button>
        </form>
      <?php endif; ?>
    </div>

    <div class="flex items-center justify-between">
      <span class="text-2xl font-bold"><?= number_format($product['price'], 2, ',', ' ') ?> €</span>
      <form action="/panier/ajouter" method="post" class="flex items-center space-x-2">
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">

        <input type="number"
               name="quantity"
               value="1"
               min="1"
               max="<?= max(0, (int)$stock) ?>"
               <?= $stock > 0 ? '' : 'disabled' ?>
               class="w-16 border rounded text-center">

        <button type="submit"
                <?= $stock > 0 ? '' : 'disabled' ?>
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-2xl <?= $stock > 0 ? '' : 'opacity-50 cursor-not-allowed' ?>">
          Ajouter au panier
        </button>
      </form>
    </div>
  </div>
</div>
